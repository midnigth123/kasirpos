<?php

namespace App\Controllers;

class KasirShift extends BaseController
{
    // ========================================================================
    // 🟢 FUNGSI 1: MEMBUKA SHIFT KASIR (INPUT SALDO AWAL)
    // ========================================================================
    public function bukaKasir()
    {
        $db = $this->db;
        $session = session();
        
        // 🎯 FIX 1: Kunci nama database outlet aktif sebelum query berjalan (Anti Eror #1046)
        $nama_db = $session->get('nama_database') ?? $session->get('db_client');
        if ($nama_db) {
            $db->setDatabase($nama_db);
        }

        $userId = $session->get('id_user');
        if (!$userId) {
            return redirect()->to(site_url('login'));
        }

        // Cek apakah ada kasir lain yang masih 'open' di outlet ini
        $cekKasirLain = $db->table('kasir_shift_transaksi')
            ->where('status', 'open') 
            ->get()
            ->getRowArray();

        if ($cekKasirLain && $cekKasirLain['id_user'] != $userId) {
            return redirect()->back()->with('error', 'Terdapat kasir lain yang masih bertugas di laci kasir saat ini.');
        }

        // 🎯 FIX 2: Bersihkan karakter titik (.) dari inputan modal awal agar masuk DB sebagai angka murni
        $modalAwal = preg_replace('/[^0-9]/', '', $this->request->getPost('modal_awal'));

        $data = [
            'id_user'         => $userId,
            'nama_shift'      => $this->request->getPost('nama_shift'),
            'tanggal_buka'    => date('Y-m-d'), 
            'modal_awal'      => $modalAwal, // Sudah steril berupa angka bersih (Contoh: 500000)
            'status'          => 'open', 
            'total_penjualan' => 0
        ];

        $db->table('kasir_shift_transaksi')->insert($data);

        // Lempar balik ke 'kasir', biarkan Kasir::index yang memutuskan masuk ke view transaksi
        return redirect()->to(site_url('kasir'))->with('pesan', 'Shift berhasil dibuka. Selamat bertugas!');
    }

    // ========================================================================
    // 🟡 FUNGSI 2: MENAMPILKAN HALAMAN HITUNG UANG FISIK (TUTUP SHIFT)
    // ========================================================================
    public function formTutupKasir()
    {
        $db = $this->db;
        $session = session();

        // 🎯 FIX 3: Pastikan database klien aktif (Gunakan fallback db_client)
        $nama_db = $session->get('nama_database') ?? $session->get('db_client');
        if ($nama_db) {
            $db->setDatabase($nama_db);
        }

        $userId = $session->get('id_user');
        if (!$userId) {
            return redirect()->to(site_url('login'));
        }

        $shiftAktif = $db->table('kasir_shift_transaksi')
            ->where(['id_user' => $userId, 'status' => 'open'])
            ->get()->getRowArray();

        // Jika shift ternyata sudah closed, jangan biarkan kasir bengong, arahkan ke absen pulang
        if (!$shiftAktif) {
            return redirect()->to(site_url('kasir/absen_pulang'))->with('info', 'Shift Anda sudah ditutup.');
        }

        // Hitung Rekap Penjualan secara presisi berdasarkan tanggal pembukaan shift kasir terkait
        $rekap = $db->table('transaksi')
            ->selectSum('total_bayar', 'total')
            ->where('id_user', $userId)
            ->where('DATE(created_at)', $shiftAktif['tanggal_buka'])
            ->get()->getRowArray();

        $data = [
            'title'      => 'Tutup Kasir / Hitung Kas',
            'shift'      => $shiftAktif,
            'penjualan'  => (float)($rekap['total'] ?? 0),
            'saldo_awal' => (float)($shiftAktif['modal_awal'] ?? 0) // Sesuaikan dengan nama field DB bos
        ];

        return view('kasir/close_kasir', $data);
    }

    // ========================================================================
    // 🔴 FUNGSI 3: MEMPROSES EKSEKUSI PENGUNCIAN SHIFT KASIR KEDALAM DB
    // ========================================================================
    public function tutupKasir()
    {
        $db = $this->db;
        $session = session();

        // 🎯 FIX 4: Sinkronisasi database toko klien agar aman dari badai eror #1046
        $nama_db = $session->get('nama_database') ?? $session->get('db_client');
        if ($nama_db) {
            $db->setDatabase($nama_db);
        }

        $userId = $session->get('id_user');
        if (!$userId) {
            return redirect()->to(site_url('login'));
        }

        // 1. Cari shift yang statusnya masih 'open'
        $shiftAktif = $db->table('kasir_shift_transaksi')
            ->where(['id_user' => $userId, 'status' => 'open'])
            ->get()
            ->getRowArray();

        if (!$shiftAktif) {
            return redirect()->to(site_url('kasir'))->with('error', 'Tidak ada shift yang aktif.');
        }

        // 2. Tangkap input uang fisik & bersihkan karakter titik non-angka (Kodingan Bos ini mantap!)
        $uangFisik = preg_replace('/[^0-9]/', '', $this->request->getPost('uang_fisik_akhir'));

        // 3. Siapkan data update 
        $dataUpdate = [
            'tanggal_tutup'    => date('Y-m-d H:i:s'), 
            'uang_fisik_akhir' => $uangFisik,
            'status'           => 'closed'
        ];

        try {
            // 4. Update database berdasarkan ID shift aktif
            $db->table('kasir_shift_transaksi')
                ->where('id', $shiftAktif['id'])
                ->update($dataUpdate);

            // 5. Lanjut ke pintu terakhir: Absen Pulang
            return redirect()->to(site_url('admin/dashboard'))->with('pesan', 'Shift berhasil ditutup.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunci data shift: ' . $e->getMessage());
        }
    }
}