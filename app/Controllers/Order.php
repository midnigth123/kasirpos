<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Order extends BaseController
{
    public function index()
    {
        $kode_toko = $this->request->getGet('toko');
        $id_meja   = $this->request->getGet('meja');

        if (!$kode_toko || !$id_meja) {
            return "<h3>Waduh! Link QR Code tidak valid.</h3>";
        }

        // --- STEP 1: Hubungkan ke DB Master ---
        $toko = $this->db->table('master_toko')
            ->where('kode_toko', $kode_toko)
            ->where('status_aktif', 'Y')
            ->get()->getRow();

        if (!$toko) {
            return "<h3>Toko tidak ditemukan atau sudah nonaktif.</h3>";
        }

        // --- STEP 2: Pindah ke Database Klien ---
        try {
            $this->db->setDatabase($toko->nama_database);
        } catch (\Exception $e) {
            return "<h3>Gagal menyambung ke server toko.</h3>";
        }

        // --- STEP 3: Ambil Data Menu & Hitung Stok Resep ---
        $produkRaw = $this->db->table('produk')
            ->whereIn('jenis_stok', ['Basah', 'Kering'])
            ->get()->getResultArray();

        // INI PERBAIKANNYA: Jalankan hitung porsi untuk tiap menu
        $produkFinal = [];
        foreach ($produkRaw as $p) {
            $p['stok_realtime'] = $this->hitungSisaPorsi($p['produk_id'], $this->db);
            $produkFinal[] = $p;
        }

        $data['produk']    = $produkFinal;
        $data['info_meja'] = $this->db->table('meja')->where('id_meja', $id_meja)->get()->getRow();
        $data['toko']      = $toko;
        $data['id_meja']   = $id_meja;
        $data['kode_toko'] = $kode_toko;

        return view('pelanggan/menu_order', $data);
    }

    public function kirim_pesanan()
    {
        if (ob_get_level() > 0) ob_end_clean();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header('Content-Type: application/json; charset=utf-8');

        if ($this->request->getMethod() === 'options') {
            exit;
        }

        $kode_toko = $this->request->getPost('kode_toko');
        $id_meja   = $this->request->getPost('id_meja'); // Ambil ID Meja

        if (!$kode_toko) {
            echo json_encode(['status' => 'error', 'message' => 'Kode toko tidak valid']);
            exit;
        }

        $toko = $this->db->table('master_toko')->where('kode_toko', $kode_toko)->get()->getRow();
        if (!$toko) {
            echo json_encode(['status' => 'error', 'message' => 'Toko tidak ditemukan']);
            exit;
        }

        // Pindah ke Database Klien
        $this->db->setDatabase($toko->nama_database);

        $dataInput = [
            'id_meja'           => $id_meja,
            'nomor_meja'        => $this->request->getPost('nomor_meja'),
            'nama_pemesan'      => $this->request->getPost('nama_pemesan') ?: 'Pelanggan',
            'item_json'         => $this->request->getPost('cart_data'),
            'total_harga'       => $this->request->getPost('total_bayar'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'status_order'      => 'Pending',
            'created_at'        => date('Y-m-d H:i:s')
        ];

        // Mulai Transaksi agar Aman
        $this->db->transBegin();

        // 1. Simpan ke Pesanan Temp
        $this->db->table('pesanan_temp')->insert($dataInput);

        // JEDOR! 2. UPDATE STATUS MEJA JADI TERISI (MERAH)
        // Supaya di dashboard kasir meja ini langsung kelihatan ada orangnya
        if (!empty($id_meja)) {
            $this->db->table('meja')
                ->where('id_meja', $id_meja)
                ->orWhere('nomor_meja', $id_meja)
                ->update(['status_meja' => 'Terisi']);
        }

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim pesanan']);
        } else {
            $this->db->transCommit();
            echo json_encode([
                'status'  => 'success',
                'message' => 'Pesanan berhasil dikirim! Silakan tunggu.'
            ]);
        }
        exit;
    }

    private function hitungSisaPorsi($id_produk, $db)
    {
        // Ambil resep untuk produk ini
        $resep = $db->table('resep')->where('id_produk_jual', $id_produk)->get()->getResultArray();

        // Jika tidak ada resep, berarti dia produk Retail (stok langsung)
        if (empty($resep)) {
            $p = $db->table('produk')->where('produk_id', $id_produk)->get()->getRowArray();
            return (float)($p['stok'] ?? 0);
        }

        $porsi_tersedia = [];
        foreach ($resep as $r) {
            $bahan = $db->table('produk')->where('produk_id', $r['id_bahan_baku'])->get()->getRowArray();
            if ($bahan && $r['jumlah_kebutuhan'] > 0) {
                // Rumus: Stok Bahan Baku / Kebutuhan per Resep
                $hitung = floor((float)$bahan['stok'] / (float)$r['jumlah_kebutuhan']);
                $porsi_tersedia[] = $hitung;
            } else {
                // Jika bahan baku tidak ditemukan, asumsikan tidak bisa buat porsi
                $porsi_tersedia[] = 0;
            }
        }

        return empty($porsi_tersedia) ? 0 : min($porsi_tersedia);
    }
}
