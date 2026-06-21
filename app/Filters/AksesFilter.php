<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AksesFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = $request->getUri()->getPath();
        if (strpos($path, 'kasir/cari_member') !== false || strpos($path, 'kasir/tambah_member') !== false) {
            return; 
        }
        $session = session();
        $role = $session->get('role');

        // 1. Izinkan Admin & MANAJER melewati filter tanpa batasan modul dasar
        // Kita tambahkan manajer di sini agar tidak kena "cegat" di bawah
        if ($role === 'admin' || $role === 'manajer' || $role === 'owner') {
            return;
        }

        $uri = service('uri');
        $path = $uri->getPath();

        // 2. Cek pengaturan akses modul untuk role lain (misalnya Kasir)
        $db = \Config\Database::connect();
        $modul = '';
        
        if (strpos($path, 'admin/produk') !== false) {
            $modul = 'produk';
        } elseif (strpos($path, 'admin/penerimaan') !== false) {
            $modul = 'penerimaan';
        }

        if ($modul !== '') {
            $cek = $db->table('pengaturan_akses')
                      ->where('role', $role)
                      ->where('modul', $modul)
                      ->where('status', 1)
                      ->get()
                      ->getRow();

            if (!$cek) {
                // Jika kasir coba akses yang bukan haknya, kembalikan ke absen/dashboard mereka
                return redirect()->to('kasir/absen')->with('error', 'Akses ditolak.');
            }
        }
    }


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosong
    }
}