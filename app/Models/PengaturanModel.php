<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'pengaturan'; // Sesuaikan dengan nama tabel di DB Anda
    protected $primaryKey       = 'penerimaan_id'; // Sesuaikan PK tabel pengaturan Anda
    protected $allowedFields    = ['nama_toko', 'logo', 'alamat', 'footer_struk']; // Sesuaikan field Anda
}