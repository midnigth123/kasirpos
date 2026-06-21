<?php

namespace App\Controllers;

class PusatTokoController extends BaseController
{
    // Fungsi untuk menampilkan halaman tabel utama
    public function index()
    {
        $dbMaster = \Config\Database::connect(); // Terhubung ke DB Pusat Anda
        
        // Ambil data semua tenant dari tabel master_toko pusat
        $data['semua_toko'] = $dbMaster->table('master_toko')->get()->getResultArray();

        return view('pusat/toko_list', $data);
    }

    // Fungsi Aksi yang dieksekusi saat Form Modal diklik "Simpan & Inject"
    public function perpanjang_toko_aksi()
    {
        $dbMaster = \Config\Database::connect();

        $idToko         = $this->request->getPost('id_toko');
        $tglJatuhTempo  = $this->request->getPost('jatuh_tempo');
        $statusAktif    = $this->request->getPost('status_aktif');

        // Ambil nama database client target
        $toko = $dbMaster->table('master_toko')->where('id', $idToko)->get()->getRow();
        if (!$toko) {
            return redirect()->back()->with('error', 'Toko tidak terdaftar.');
        }

        $dbNameClient = $toko->nama_database;

        // KONDISI 1: Update data di DB Master Pusat
        $dbMaster->table('master_toko')
            ->where('id', $idToko)
            ->update([
                'jatuh_tempo'  => $tglJatuhTempo,
                'status_aktif' => $statusAktif
            ]);

        // KONDISI 2: Berpindah koneksi dan Inject Update langsung ke dalam DB Tenant Client
        try {
            $dbMaster->setDatabase($dbNameClient);

            $dbMaster->table('master_toko')
                ->where('nama_database', $dbNameClient)
                ->update([
                    'jatuh_tempo'  => $tglJatuhTempo,
                    'status_aktif' => $statusAktif
                ]);

            // Kembalikan ke setelan database default pusat
            $dbMaster->setDatabase(\Config\Database::connect()->getDatabase());

            return redirect()->back()->with('success', 'Masa aktif toko berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update database client: ' . $e->getMessage());
        }
    }
}