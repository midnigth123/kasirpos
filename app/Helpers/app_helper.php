<?php

if (!function_exists('cek_akses')) {
    function cek_akses($role, $modul)
    {
        $db = \Config\Database::connect();
        $akses = $db->table('pengaturan_akses')
                    ->where('role', $role)
                    ->where('modul', $modul)
                    ->where('status', 1)
                    ->get()
                    ->getRow();

        return $akses ? true : false;
    }
}
