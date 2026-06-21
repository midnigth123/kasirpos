<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPenerimaanModel extends Model
{
    protected $table            = 'penerimaan_detail'; 
    protected $primaryKey       = 'detail_id';
    
    protected $allowedFields    = [
        'penerimaan_id', 
        'produk_id', 
        'jumlah_masuk', 
        'harga_beli_baru'
    ];

    // JEDOR! Tambahkan Constructor ini agar detail barang masuk ke DB Toko yang benar
    public function __construct(\CodeIgniter\Database\ConnectionInterface $db = null)
    {
        // Menangkap koneksi dinamis dari Controller (Admin.php)
        parent::__construct($db);
    }
}