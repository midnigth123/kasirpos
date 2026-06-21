<?php

namespace App\Models;

use CodeIgniter\Model;

class StokOpnameModel extends Model
{
    protected $table         = 'stok_opname';
    protected $primaryKey    = 'opname_id';
    protected $allowedFields = [
        'produk_id', 
        'stok_sistem', 
        'stok_fisik', 
        'selisih', 
        'keterangan', 
        'username'
    ];
    protected $useTimestamps = false; 

    // JEDOR! Tambahkan Constructor ini agar Model bisa ikut pindah database
    public function __construct(\CodeIgniter\Database\ConnectionInterface $db = null)
    {
        // Oper koneksi dari Controller ke Core Model CodeIgniter
        parent::__construct($db); 
    }
}