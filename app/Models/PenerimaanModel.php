<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanModel extends Model
{
    protected $table            = 'penerimaan_barang'; 
    protected $primaryKey       = 'penerimaan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    protected $allowedFields    = [
        'kode_penerimaan', 
        'tanggal_masuk', 
        'tgl_expired',
        'supplier', 
        'status', 
        'created_at'
    ];

    // JEDOR! Tambahkan Constructor ini agar data penerimaan masuk ke DB Toko yang benar
    public function __construct(\CodeIgniter\Database\ConnectionInterface $db = null)
    {
        // Menangkap koneksi dinamis $this->db dari Controller
        parent::__construct($db);
    }
}