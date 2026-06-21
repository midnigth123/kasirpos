<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminGuard implements FilterInterface
{
   public function before(RequestInterface $request, $arguments = null)
{
    if (!session()->get('logged_in')) {
        return redirect()->to('/login');
    }
    
    $role = session()->get('role');
    $allowedRoles = ['admin', 'owner', 'manajer', 'kasir']; // Tambahkan 'kasir' di sini

    if (!in_array($role, $allowedRoles)) {
        session()->setFlashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
        return redirect()->to('/login');
    }
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosong
    }
}