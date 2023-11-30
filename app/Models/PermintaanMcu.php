<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanMcu extends Model
{
    use HasFactory;
    protected $table = 'permintaan_mcu';
    protected $primaryKey = 'id_permintaan';
    protected $guarded = [];
    // protected $fillable = [
    //     'id_permintaan',
    //     'no_rm',
    //     'no_registrasi',
    //     'nik',
    //     'nama',
    //     'alamat',
    //     'tanggal_order',
    //     'tanggal_kunjungan',
    //     'jenis_kelamin',
    //     'tanggal_lahir',
    //     'telepon',
    //     'status_pembayaran',
    //     'metode_pembayaran',
    //     'status_pasien',
    //     'created_at',
    //     'updated_at',
    //     'jenis_mcu',
    //     'tempat_lahir',
    //     'jarak_ke_lokasi',
    //     ''
    // ];
}
