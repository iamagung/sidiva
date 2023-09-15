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
		$col_where = ($params->kirim_via == 'wa') ? ['id', $params->wa] : ['email', $params->email];
		return Authentication::where([
			$col_where,
			['otp', $params->kode_otp],
			['expired', '0'],
		])->first();
	}
	public static function update_expired($params){
		$col_where = ($params->kirim_via == 'wa') ? ['id', $params->wa] : ['id', $params->email];
		$cek = Authentication::where([
			$col_where,
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
