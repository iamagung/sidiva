<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class ResepObatDetail extends Model
{
    use HasFactory;
    public function __construct() {
        $this->setConnection('mysql');
        $this->table = Config::get('database.connections.mysql.database', 'sidiva').'.resep_obat_detail';
        $this->primaryKey = 'id_resep_obat_detail';
    }

    public function resep_obat()
    {
        return $this->belongsTo(ResepObat::class, 'resep_obat_id', 'id_resep_obat');
    }
}
