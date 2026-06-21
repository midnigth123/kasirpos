<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table      = 'produk';
    protected $primaryKey = 'produk_id'; 

    protected $allowedFields = [
        'barcode', 
        'nama_produk', 
        'harga_beli', 
        'harga_jual', 
        'stok', 
        'kategori',
        'jenis_stok',
        'img'
    ];

    // JEDOR! Tambahkan ini agar data produk tidak nyasar ke DB Utama
    public function __construct(\CodeIgniter\Database\ConnectionInterface $db = null)
    {
        // Menangkap operan $this->db dari Admin.php
        parent::__construct($db);
    }
}