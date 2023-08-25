<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
class Helpers{
	public static function dateIndo($param,$request='tanggal'){
		$bulan = [
			1 => 'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		];
		$split = explode('-', $param);
		if($request=='hari'){ # Dengan nama hari
			$hari = Helpers::getDay(date('D',strtotime($param)));
			return $hari . ' ' . $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
		}else{ # Tanpa nama hari
			return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
		}
	}

	public static function getDay($param){
		if($param=='Sun'){
			$hari = 'Minggu';
		}elseif($param=='Mon'){
			$hari = 'Senin';
		}elseif($param=='Tue'){
			$hari = 'Selasa';
		}elseif($param=='Wed'){
			$hari = 'Rabu';
		}elseif($param=='Thu'){
			$hari = 'Kamis';
		}elseif($param=='Fri'){
			$hari = 'Jumat';
		}else{
			$hari = 'Sabtu';
		}
		return $hari;
	}

	# Generate no rm
	public static function generateRM(){
		$getKode = DB::connection('dbrsud')->table('tm_customer')->max('KodeCust');
		$num = (int)substr($getKode, 5);
		return $nextKode = 'W'.date("ym").(string)($num+1);
	}

	# Generate no registrasi mcu
	public static function generateNoRegMcu($request)
	{
		$prefix = 'Reg-';
		$length = strlen($prefix)+3;
		$regist = DB::table('permintaan_mcu')->select('no_registrasi')
				->where('tanggal_kunjungan',$request->tanggal_kunjungan)
				->whereRaw("LENGTH(no_registrasi)=$length")
				->where('no_registrasi','like',"$prefix%")
				->orderBy('no_registrasi','desc')->first();
		$num = 0;
		if(!empty($regist)){
			$num = (int)substr($regist->no_registrasi, -3);
		}
		$reg       			= sprintf("%03d",$num+1);
		return $nextAntri 	= "$prefix".$reg;
	}

	# Generate no registrasi homecare
	public static function generateNoRegHc($request)
	{
		$prefix = 'Reg-';
		$length = strlen($prefix)+3;
		$regist = DB::table('permintaan_mcu')->select('no_registrasi')
				->where('tanggal_kunjungan',$request->tanggal_kunjungan)
				->whereRaw("LENGTH(no_registrasi)=$length")
				->where('no_registrasi','like',"$prefix%")
				->orderBy('no_registrasi','desc')->first();
		$num = 0;
		if(!empty($regist)){
			$num = (int)substr($regist->no_registrasi, -3);
		}
		$reg       			= sprintf("%03d",$num+1);
		return $nextAntri 	= "$prefix".$reg;
	}

	# Random string
	public static function randomString($length){
		$characters = '0123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
		// $characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	# Callback pendaftaran mcu
	public static function callbackRegistMcu($params)
	{
		$permintaan =  DB::table('permintaan_mcu')->where('id_permintaan', $params->id_permintaan)->first();
		$id_layanan = explode(",", $permintaan->layanan_id);
		$layanan = DB::table('layanan_mcu')->whereIn('id_layanan', $id_layanan)->get();

		return [
			'data' 		=> $permintaan,
			'layanan' 	=> $layanan
		];
	}

	# Callback pendaftaran homecare
	public static function callbackRegistHc($params)
	{
		$permintaan 	= DB::table('permintaan_hc')->where('id_permintaan_hc', $params->id_permintaan_hc)->first();
		$paket    		= DB::table('paket_hc')->where('id_paket_hc', $permintaan->paket_hc_id)->first();
		$jumlahKm		= Helpers::calculateDistance($permintaan->latitude, $permintaan->longitude);
		$biayaTotal  	= (int)$permintaan->biaya_layanan + (int)$permintaan->biaya_ke_lokasi;
		return [
			'data' 		=> $permintaan,
			'paket' 	=> $paket,
			'jumlahKm'	=> $jumlahKm." Km",
			'total'		=> $biayaTotal
		];
	}

	# Generate no antrian
	public static function generateNoantrian(Request $request)
	{
		$prefix = 'M';
		$length = strlen($prefix)+3;
		$antri = DB::table('permintaan_mcu')->select('no_antrian')
				->where('tanggal_kunjungan',$request->tanggal_kunjungan)
				->whereRaw("LENGTH(no_antrian)=$length")
				->where('no_antrian','like',"$prefix%")
				->orderBy('no_antrian','desc')->first();
		$num = 0;
		if(!empty($antri)){
			$num = (int)substr($antri->no_antrian, -3);
		}
		$angkaAntri       = sprintf("%03d",$num+1);
		return $nextAntri = "$prefix".$angkaAntri;
	}

	# Menghitung jarak dari rsud wahidin ke lokasi pasien
	public static function calculateDistance($latitude, $longitude)
    {
        $lat1 = "-7.4906403"; // latitude rsud wahidin
        $lon1 = "112.4178198"; // longitude rsud wahidin
        $lat2 = $latitude;
        $lon2 = $longitude;

        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1))) * sin(deg2rad($lat2)) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $rMiles = $miles * 60 * 1.1515;
        $kilometers = $rMiles * 1.609344;
        $return = intval($kilometers);
        return $return;
    }

	# Custom response start
	public static function resInternal($msg,$code=500,$data=[]){ # Template rest internal
		return response()->json([
			'metadata' => [
				'message' => $msg,
				'code'    => $code,
			],
			'response' => $data,
		]);
	}
	public static function resApi($msg='Terjadi kesalahan sistem',$code=500,$data=[]){ # Template rest api
		return response()->json([
			'metadata' => [
				'message' => $msg,
				'code'    => $code,
			],
			'response' => $data,
		],$code);
	}
	# Custom response end
	
	# Logging start
	public static function logging($param=[]){
		# Modify parameter for logging start
		for($i=0; $i<5; $i++){
			$arr[$i] = isset($param[$i]) ? $param[$i] : (
				$i==0 ? 'NO MESSAGES' : (
					$i==1 ? false : '-'
				)
			);
		}
		# Modify parameter for logging end

		$title   = $arr[0];
		$status  = $arr[1]; # Status => true{jika program berhasil}, false{jika program gagal}
		$errMsg  = $arr[2];
		$errLine = $arr[3];
		$data    = $arr[4];

		$res = [
			$title => [
				'messageErr' => $errMsg,
				'line'       => $errLine,
				'data'       => $data,
			]
		];
		if($status){ # If $status => true, unset key
			unset($res[$title]['messageErr'],$res[$title]['line']);
		}
		Log::info(json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		return true;
	}
	# Logging end
}