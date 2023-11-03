<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingTelemedicine extends Model
{
    use HasFactory;
    protected $table = 'rating_telemedicine';
    protected $primaryKey = 'id_rating_telemedicine';

    public function permintaan()
    {
        return $this->belongsTo('App\Models\PermintaanTelemedicine', 'permintaan_telemedicine_id', 'id_permintaan_telemedicine');
    }
}
