<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user'; 
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'username', 'password', 'nama_user', 'role', 'alamat', 'no_hp'
    ];

    // JEDOR! WAJIB ADA DI SEMUA MODEL
    public function __construct(\CodeIgniter\Database\ConnectionInterface $db = null)
    {
        parent::__construct($db); 
    }

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            // Agar saat update tanpa ganti password, data lama tidak hilang/rusak
            unset($data['data']['password']);
        }
        return $data;
    }
}