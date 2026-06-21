<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['form', 'url', 'auth'];
    protected $session;
    protected $db;

    // Di dalam file BaseController.php
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $dbClient = $this->session->get('db_client');

        // Jika ada session pakai db_kasir2 (atau lainnya), jika tidak pakai db_kasir
        $targetDB = ($dbClient) ? $dbClient : 'db_kasir';

        $dbConfig = config('Database');
        $dbConfig->default['database'] = $targetDB;

        // Paksa koneksi baru (shared = false)
        $this->db = \Config\Database::connect('default', false);

        // Suntik Service agar semua model ikut pindah
        \Config\Services::injectService('database', $this->db);

        $this->db->setDatabase($targetDB);
    }
    // Tambahkan di dalam class BaseController
    protected function update_kartu_stok($id_produk, $tipe, $jumlah, $referensi, $keterangan)
    {
        // 1. Ambil stok saat ini dari tabel produk (SEBELUM diupdate)
        $produk = $this->db->table('produk')->where('produk_id', $id_produk)->get()->getRow();

        $stok_awal = $produk ? $produk->stok : 0;
        $stok_masuk = 0;
        $stok_keluar = 0;

        // 2. Tentukan apakah dia masuk atau keluar
        if (in_array($tipe, ['masuk', 'mutasi_masuk', 'opname_tambah'])) {
            $stok_masuk = $jumlah;
            $stok_akhir = $stok_awal + $jumlah;
        } else {
            $stok_keluar = $jumlah;
            $stok_akhir = $stok_awal - $jumlah;
        }

        // 3. JEDOR! Simpan ke tabel kartu_stok
        $this->db->table('kartu_stok')->insert([
            'produk_id'      => $id_produk,
            'tipe'           => $tipe,
            'kode_referensi' => $referensi,
            'stok_awal'      => $stok_awal,
            'stok_masuk'     => $stok_masuk,
            'stok_keluar'    => $stok_keluar,
            'stok_akhir'     => $stok_akhir,
            'keterangan'     => $keterangan,
            'tanggal'        => date('Y-m-d H:i:s')
        ]);
    }
}
