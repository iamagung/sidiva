<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $table = 'rating';
    protected $primaryKey = 'id_rating';

    public function permintaan_telemedicine()
    {
        return $this->belongsTo('App\Models\PermintaanTelemedicine', 'permintaan_id', 'id_permintaan_telemedicine');
    }

    public function permintaan_hc()
    {
        return $this->belongsTo('App\Models\PermintaanHC', 'permintaan_id', 'id_permintaan_hc');
    }
}
