<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AksesSaaSFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Ambil URL mentah yang sedang diakses browser saat ini
        $currentUrl = $_SERVER['REQUEST_URI'];

        // JALUR EVAKUASI MUTLAK: Jika URL mengandung kata 'tagihan', 'logout', atau 'cek_lisensi_toko', langsung LOLOSKAN!
        // Ditambahkan 'cek_lisensi_toko' agar radar background tidak tersumbat putaran loop filter
        if (
            strpos($currentUrl, 'tagihan') !== false || 
            strpos($currentUrl, 'logout') !== false || 
            strpos($currentUrl, 'cek_lisensi_toko') !== false
        ) {
            return;
        }

        $session = session();

        // 2. Jika belum login, loloskan ke halaman login biasa
        if (!$session->get('logged_in')) {
            return;
        }

        $role     = $session->get('role');
        $dbClient = $session->get('db_client');

        // DEVELOPER/SUPER ADMIN AMAN: Jangan kunci akun admin pusat Anda sendiri
        if ($role === 'admin' || $role === 'admin_pusat') {
            return;
        }

        if (!empty($dbClient)) {
            $db = \Config\Database::connect();
            
            try {
                // 🎯 FIX UTAMA: Tarik data konfigurasi tenant dari DB master DULU sebelum koneksi dipindah
                // Ini mencegah error "Table master_toko not found" di database client
                $toko = $db->table('master_toko')
                           ->where('nama_database', $dbClient)
                           ->get()
                           ->getRow();

                if ($toko) {
                    $hariIni    = date('Y-m-d');
                    $jatuhTempo = $toko->jatuh_tempo;

                    // PENGUNCI 1: Jika status_aktif dinonaktifkan ('N')
                    if (trim($toko->status_aktif) !== 'Y') {
                        $session->destroy();
                        return redirect()->to(site_url('login'))->with('error', 'Akses toko ditangguhkan oleh sistem pusat.');
                    }

                    // PENGUNCI 2: Jika masa langganan habis (melewati jatuh_tempo)
                    if (!empty($jatuhTempo) && $hariIni > $jatuhTempo) {
                        // Lempar ke ruang isolasi tagihan
                        return redirect()->to(site_url('admin/tagihan'))->with('expired_msg', 'Masa aktif langganan toko Anda telah berakhir pada ' . date('d-m-Y', strtotime($jatuhTempo)));
                    }
                }

                // 🎯 BELOKKAN SEKARANG: Setelah lolos semua pengunci lisensi, barulah koneksi aman dipindah ke database client aktif!
                $db->setDatabase($dbClient);

            } catch (\Exception $e) {
                // Aman jika DB loss connection
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosongkan
    }
}