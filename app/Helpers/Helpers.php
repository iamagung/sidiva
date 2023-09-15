<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use App\Models\User;
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
	# Check Regist Pasien
	public static function checkRegistPasien($checkBy, $param) {
		if ($checkBy=='nik') {
			$user=User::where('username',$param)->first();
		}else{
			$user=User::where('telepon',$param)->first();
		}
		return $user;
	}
	# Text Berhasil Send OTP
	public static function textSendOtp($otp){
		$text = "Kode OTP anda adalah *$otp*";
		return $text;
	}
	# Message send otp
	public static function messageSenderOtp($params){
		$prepareMessage = [[ # Array 2 dimensi
			'phone' => $params->phone,
			'message' => $params->message,
		]];
		if(isset($params->toAdmin) && $params->toAdmin === true){ # toAdmin === true >> gagal kirim pesan ke pasien, jadi kirimkan ke admin
			$prepareMessage[0]['phone'] = config('webhook.phone'); # Replace nomor pasien jadi nomor admin
			if(count($nomorAdmin = ChatBotReport::limit(5)->get()) > 0){ # Jika $nomorAdmin ada, replace value $prepareMessage
				$prepareMessage = []; # Set value jadi array kosong
				foreach($nomorAdmin as $key => $val){
					$forPush = [
						'phone' => $val->phone,
						'message' => $params->message,
					];
					array_push($prepareMessage,$forPush);
				}
			}
		}
		$payload = [
			'payload' => ["data" => $prepareMessage],
			'token' => config('webhook.key'),
			'url' => config('webhook.send.type.message'),
		];
		return Requestor::sendMultipleChat($payload); # Send message to admin
	}
	# Prepare message for SYSTEM ERROR
	public static function sendErrorSystemToAdmin($params = []){
		date_default_timezone_set('Asia/Jakarta');
		try{
			$title = isset($params['title']) ? strtoupper($params['title']) : 'SYSTEM ERROR';
			$text = "*$title*";
			$text .= "\n*DATE :* _".date('d-m-Y')."_";
			$text .= "\n*TIME :* _".date('H:i:s')."_";

			$message = isset($params['message']) ? $params['message'] : 'Terjadi kesalahan sistem';
			$arrKeys = ['url','file','line','message','data'];
			foreach($arrKeys as $key => $val){
				if(isset($params[$val])){
					$upper = strtoupper($val);
					if($val=='data'){
						$text .= "\n*$upper :* ".json_encode($params[$val],JSON_PRETTY_PRINT);
					}else if($val=='message'){
						$text .= "\n*$upper :* _".$message."_";
					}else{
						$text .= "\n*$upper :* _".$params[$val]."_";
					}
				}
			}
			return self::messageSenderError(['message' => $text]);
		}catch(\Throwable $e){
			$arrLog = [
				'title'   => 'FAILED PREPARE MESSAGE FOR SYSTEM ERROR',
				'url'     => request()->url(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine(),
				'message' => $e->getMessage(),
			];
			Helpers::logging($arrLog); # Log info
		}
	}
	# Message send error otp
	public static function messageSenderError($params = []){
		$phone = config('webhook.phone'); # Nomor admin get from ENV
		$text = isset($params['message']) ? $params['message'] : "_Terjadi kesalahan sistem_";
		$prepareMessage = [[ # Array 2 dimensi
			'phone' => $phone,
			'message' => $text,
		]];
		if(count($nomorAdmin = ChatBotReport::limit(5)->get()) > 0){ # Jika $nomorAdmin ada, replace value $prepareMessage
			$prepareMessage = []; # Set value jadi array kosong
			foreach($nomorAdmin as $key => $val){
				$forPush = [
					'phone' => $val->phone,
					'message' => $text,
				];
				array_push($prepareMessage,$forPush);
			}
		}
		$payload = [
			'payload' => ["data" => $prepareMessage],
			'token' => config('webhook.key'),
			'url' => config('webhook.send.type.message'),
		];
		return Requestor::sendMultipleChat($payload); # Send message to admin
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