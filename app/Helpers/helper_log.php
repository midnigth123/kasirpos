<?php

if (!function_exists('tambah_log')) {
    function tambah_log($user, $role, $aktivitas) {
        $db = \Config\Database::connect();
        
        $data = [
            'user'      => $user,
            'role'      => $role,
            'aktivitas' => $aktivitas,
            'waktu'     => date('Y-m-d H:i:s')
        ];
        
        $db->table('log_aktivitas')->insert($data);
    }
}