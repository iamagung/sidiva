<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authentication extends Model
{
    use HasFactory;
    protected $table = 'authentication';
    protected $primaryKey = "id";

    public static function insert($params){
		$save = new Authentication;
        $save->wa = $params->wa;
		$save->otp = $params->otp;
		$save->expired = 0;
		$save->tanggal_waktu = $params->tgljam;
		$save->save();
		return ($save) ? $save : false;
	}
	public static function check_otp($params){
		return Authentication::where([
			['otp', $params->kode_otp],
			['expired', '0'],
		])->first();
	}
	public static function update_expired($params){
		$cek = Authentication::where([
			['otp', $params->kode_otp],
			['expired', '0'],
		])->first();
		if ($cek) {
			$cek->expired = 1;
			$cek->save();
		}
		return ($cek) ? true : false;
	}
}
