<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingTelemedicine extends Model
{
    use HasFactory;
    protected $table = 'rating';
    protected $primaryKey = 'id_rating';

    public function permintaan_telemedicine()
    {
        return $this->belongsTo('App\Models\PermintaanTelemedicine', 'permintaan_id', 'id_permintaan_telemedicine');
    }
}
