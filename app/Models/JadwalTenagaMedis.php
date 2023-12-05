<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalTenagaMedis extends Model
{
    use HasFactory;
    protected $table = 'jadwal_tenaga_medis';
    protected $primaryKey = 'id_jadwal_tenaga_medis';

    /**
     * Get the tenagaMedis that owns the JadwalTenagaMedis
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenagaMedis()
    {
        return $this->belongsTo('App\Models\TenagaMedisTelemedicine', 'nakes_id', 'nakes_id');
    }
}
