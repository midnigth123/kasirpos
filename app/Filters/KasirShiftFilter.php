<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class KasirShiftFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $role    = $session->get('role');
        
        // Gunakan getPath() agar lebih konsisten di berbagai server
        $path = $request->getUri()->getPath();

        // --- 1. JALUR PENGECUALIAN (WAJIB LOLOS) ---
        // Kita gunakan keyword saja agar lebih fleksibel
        $excluded_keywords = [
            'login', 
            'logout', 
            'kasir/absen', 
            'kasir/simpan_absen', 
            'kasir/absen_pulang',
            'kasir/simpan_absen_pulang'
        ];

        foreach ($excluded_keywords as $keyword) {
            if (strpos($path, $keyword) !== false) {
                return; // JEDOR! Langsung lolos tanpa cek apapun di bawah
            }
        }

        // --- 2. PROTEKSI LOGIN ---
        if (!$role) {
            return redirect()->to(site_url('login'));
        }

        // --- 3. ADMIN, OWNER & MANAJER BEBAS ---
        // REVISI SAAS: Tambahkan 'owner' ke dalam array agar tidak kena cegat absen kasir!
        if (in_array($role, ['admin', 'owner', 'manajer'])) {
            return; // Langsung lolos, hentikan pemeriksaan filter di sini
        }

        // --- 4. LOGIKA KASIR ---
        if ($role === 'kasir') {
            // Cek Session dulu (Paling Cepat)
            if ($session->get('is_absen')) {
                return;
            }

            // Jika session kosong, baru cek DB sebagai cadangan
            $db = \Config\Database::connect();
            $userId = $session->get('id_user');
            
            $cekAbsen = $db->table('absensi')
                ->where('id_user', $userId)
                ->where('tanggal', date('Y-m-d'))
                ->get()->getRow();

            if (!$cekAbsen) {
                // JEDOR! Pastikan tidak redirect kalau memang jalurnya sudah di halaman absen
                return redirect()->to(site_url('kasir/absen'))->with('error', 'Absen dulu bos!');
            } else {
                // Kunci di session agar filter tidak berat cek DB terus-menerus
                $session->set('is_absen', true);
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosong
    }
}