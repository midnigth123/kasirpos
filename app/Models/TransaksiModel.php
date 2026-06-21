<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table            = 'transaksi';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['invoice', 'total_bayar', 'created_at'];

    // JEDOR! Gunakan Type Hinting yang tepat
    public function __construct(\CodeIgniter\Database\ConnectionInterface $db = null)
    {
        parent::__construct($db);
    }

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}