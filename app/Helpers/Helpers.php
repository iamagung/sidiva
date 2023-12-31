<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use App\Models\User;
use App\Models\PaymentPermintaan;
use App\Models\ResepObat;
use App\Models\ResepObatDetail;
use App\Models\PermintaanTelemedicine;
class Helpers{
	# Sender otp start
	public static function messageSenderOtp($params){
		try {
			$curl = curl_init();
			$token = "Qwf4jUkeX3h6OwNpWjzsg82stjUYWcx0tsxXc7vfLgva3Iap3nxPzlO0yrfDPGCl";
			$code_otp = $params->otp;
			$pesan = 'Selamat registrasi berhasil.';
			$pesan .= "\nKode verifikasi anda : *$code_otp*";
			$payload = [
				"data" => [
					[
						'phone' => $params->phone,
						'message' => $pesan,
					]
				]
			];
			curl_setopt($curl, CURLOPT_HTTPHEADER,
				array(
					"Authorization: $token",
					"Content-Type: application/json"
				)
			);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload) );
			curl_setopt($curl, CURLOPT_URL,  "https://jogja.wablas.com/api/v2/send-message");
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($curl);
			curl_close($curl);
			return json_decode($result);
		} catch (\Throwable $e) {
			$log = ['ERROR SEND OTP ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            self::logging($log);
            return self::resApi('Terjadi kesalahan sistem',500);
		}

	}
	# Sender otp end
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
	public static function generateNoRegMcu($tanggal) {
		$prefix = 'M-';
		$length = strlen($prefix)+3;
		$regist = DB::table('permintaan_mcu')->select('no_registrasi')
				->where('tanggal_kunjungan',$tanggal)
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
	public static function generateNoRegHc($request) {
		$prefix = 'H-';
		$length = strlen($prefix)+3;
		$regist = DB::table('permintaan_hc')->select('no_registrasi')
				->where('tanggal_kunjungan',date('Y-m-d', strtotime($request->tanggal_kunjungan)))
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
	# Generate no registrasi telemedicine
	public static function generateNoRegTelemedicine($request) {
		$prefix = 'T-';
		$length = strlen($prefix)+3;
		$regist = DB::table('permintaan_telemedicine')->select('no_registrasi')
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
	# Generate no resep telemedicine
	public static function generateNoResepTelemedicine() {
		$prefix = 'RCP/TELE/';
		$tahun = date('Y/m');
		$prefix .= $tahun.'/';
		$regist = ResepObat::select('no_resep')
				->where('no_resep','like',"$prefix%")
				->orderBy('no_resep','desc')->first();
		$num = 0;
		if(!empty($regist)){
			$num = (int)substr($regist->no_resep, -6);
		}
		$reg       			= sprintf("%06d",$num+1);
		return $nextAntri 	= "$prefix".$reg;
	}
	# Generate no resep telemedicine
	public static function generateNoResepHc() {
		$prefix = 'RCP/HOCA/';
		$tahun = date('Y/m');
		$prefix .= $tahun.'/';
		$regist = ResepObat::select('no_resep')
				->where('no_resep','like',"$prefix%")
				->orderBy('no_resep','desc')->first();
		$num = 0;
		if(!empty($regist)){
			$num = (int)substr($regist->no_resep, -6);
		}
		$reg       			= sprintf("%06d",$num+1);
		return $nextAntri 	= "$prefix".$reg;
	}
    # Generate no invoice local
	public static function generateInvoice() {
		$prefix = 'INV/';
		$tahun = date('Ymd');
		$prefix .= $tahun.'/';
		$regist = PaymentPermintaan::select('nomor_referensi')
				->where('nomor_referensi','like',"$prefix%")
				->orderBy('nomor_referensi','desc')->first();
		$num = 0;
		if(!empty($regist)){
			$num = (int)substr($regist->no_resep, -6);
		}
		$reg       			= sprintf("%06d",$num+1);
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
	public static function dataRegistrasiMCU($params) {
		$price = 0;
		$permintaan = DB::table('permintaan_mcu')->where('id_permintaan', $params)->first();
		$layanan = DB::table('layanan_permintaan_mcu as lpm')
			->leftJoin('layanan_mcu as lm','lm.id_layanan','lpm.layanan_id')
			->where('lpm.permintaan_id', $params)->get();
		foreach ($layanan as $key => $val) {
			$price += $val->harga;
		}
		$biayaTotal  	= $price;
		if ($permintaan->tempat_mcu!='RS') {
			$biayaTotal  	= $price + $permintaan->biaya_ke_lokasi;
		}
		$payment = DB::table('payment_permintaan')->where('jenis_layanan', 'mcu')->where('permintaan_id',$params)->first();
		return [
			'data' 	   => $permintaan,
			'layanan'  => $layanan,
			'subtotal' => $biayaTotal,
			'payment'  => $payment
		];
	}
	# Callback pendaftaran homecare
	public static function dataRegistrasiHomecare($params) {
		$price = 0;
		$permintaan = DB::table('permintaan_hc')->where('id_permintaan_hc', $params)->first();
		$layanan = DB::table('layanan_permintaan_hc as lphc')
			->leftJoin('layanan_hc as lhc','lhc.id_layanan_hc','lphc.layanan_id')
			->where('lphc.permintaan_id', $params)->get();
		foreach ($layanan as $key => $val) {
			$price += $val->harga;
		}
		$biayaTotal  	= $price + (int)$permintaan->biaya_ke_lokasi;
		$payment = DB::table('payment_permintaan')->where('jenis_layanan', 'homecare')->where('permintaan_id',$params)->first();
		return [
			'data' 	   => $permintaan,
			'layanan'  => $layanan,
			'subtotal' => $biayaTotal,
			'payment'  => $payment
		];
	}
	# Callback pendaftaran telemedicine
	public static function dataRegistrasiTelemedicine($params) {
        $price = 0;
		$permintaan = DB::table('permintaan_telemedicine')->where('id_permintaan_telemedicine', $params)->first();
		$biayaTotal = $permintaan->biaya_layanan;
        $layanan = array();
        $layanan[] = (object)[
            'nama_layanan' => 'telemedicine',
            'harga' => $permintaan->biaya_layanan
        ];
		$payment = DB::table('payment_permintaan')->where('jenis_layanan', 'telemedicine')->where('permintaan_id',$params)->first();
		return [
			'permintaan' => $permintaan,
            'layanan' => $layanan,
			'subtotal' => $biayaTotal,
			'payment'  => $payment,
		];
	}
    # Callback eresep telemedicine
	public static function dataEresepTelemedicine($params) {
        $price = 0;
		$permintaan = DB::table('permintaan_telemedicine')->where('id_permintaan_telemedicine', $params)->first();
        $resep = DB::table('resep_obat')->where('jenis_layanan', 'telemedicine')->where('permintaan_id', $params)->first();
        $payment = DB::table('payment_permintaan')->where('jenis_layanan', 'eresep_telemedicine')->where('permintaan_id',$params)->first();

        $resepDetail = DB::table('resep_obat_detail')->where('resep_obat_id', $resep->id_resep_obat);
        $resepDetail->when(($payment->status!='SETTLED'&&$payment->status!='PAID'), fn($q) =>
            $q->selectRaw('(CASE WHEN (LENGTH(nama_obat)>=3) THEN CONCAT(SUBSTRING(nama_obat, 1, 2) , "****") WHEN (LENGTH(nama_obat)=2) THEN CONCAT(nama_obat, "****") ELSE CONCAT(nama_obat, "*****") END) AS nama_obat,resep_obat_id,kode_obat,qty,signa,harga')
        );

        $resepDetail->when(($payment->status=='SETTLED'||$payment->status=='PAID'), fn($q) =>
            $q->selectRaw('nama_obat,resep_obat_id,kode_obat,qty,signa,harga')
        );

        $resepDetail = $resepDetail->get();
        if($resep->diantar == "ya") {
            $biayaTotal = (float)$payment->nominal + $payment->ongkos_kirim;
        } else {
            $biayaTotal = $payment->nominal;
        }
		return [
			'permintaan' => $permintaan,
            'resep' => $resep,
            'resepDetail' => $resepDetail,
			'subtotal' => $biayaTotal,
			'payment'  => $payment
		];
	}
	# Callback pendaftaran mcu
	public static function callbackRegistTelemedicine($params)
	{
		$permintaan =  DB::table('permintaan_telemedicine')->where('id_permintaan_telemedicine', $params->id_permintaan_telemedicine)->first();

		return [
			'data' 		=> $permintaan
		];
	}
	# Generate no antrian
	public static function generateNoantrian(Request $request) {
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
	# Check Regist Pasien
	public static function checkRegistPasien($checkBy, $param) {
		if ($checkBy=='nik') {
			$user=User::where('username',$param)->first();
		}else{
			$user=User::where('telepon',$param)->first();
		}
		return $user;
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
			'metaData' => [
				'message' => $msg,
				'code'    => $code,
			],
			'response' => $data,
		],$code);
	}
	public static function resAjax($data=[]){
		$keyData = ['message','code','response'];
		$arr = [];
		foreach($keyData as $key => $val){
			$arr[$val] = isset($data[$val]) ? $data[$val] : ( # Cek key, apakah sudah di set
				$val=='code' ? 500 : (
					$val=='message' ? '-' : []
				)
			);
		}

		$code = $arr['code'];
		$msg = $arr['message'];

		$metadata = [
			'code'    => $arr['code'],
			'message' => $arr['message'],
		];
		$payload['metadata'] = $metadata;
		if($code>=200 && $code<250){
			$payload['response'] = $arr['response'];
		}
		return response()->json($payload,$code);
	}
	public static function custom_response($code, $status, $msg, $data){ # Rest api with http response
		return response()->json([
			'metaData' => [
				'code' => $code,
				'status' => $status,
				'message' => $msg,
			],
			'response' => $data,
		], $code);
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
	public static function custom_logging($param=[]){ # Parameter using key-value
		$keyForLog = ['status','url','file','title','message','line','data']; # Declar key param log, tambahkan value di baris ini jika ingin menambah parameter untuk log
		$arr = [];
		# Modify params for logging start
		foreach($keyForLog as $key => $val){
			$arr[$val] = isset($param[$val]) ? $param[$val] : ( # Cek key, apakah sudah di set
				$val=='status' ? false : ( # Jika key "status" belum di-set, isi value menjadi "false" :bool
					$val=='title' ? 'NO TITLE' : (
						$val=='message' ? 'NO MESSAGES' : '-'
					)
				)
			);
		}
		# Modify params for logging end

		$status = $arr['status']; # Status : true{program berhasil}, false{program gagal / program berhasil tapi data tidak ditemukan}
		$url    = $arr['url'];
		$file   = $arr['file'];
		$title  = $arr['title'];
		$error  = $arr['message'];
		$line   = $arr['line'];
		$data   = $arr['data'];
		$res = [
			$title => [
				'url'     => $url,
				'file'    => $file,
				'message' => $error,
				'line'    => $line,
				'data'    => $data,
			]
		];
		if($status){ # $status == true => unset key {"error","line"}
			unset($res[$title]['file'],$res[$title]['message'],$res[$title]['line']);
		}
		Log::info(json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		return true;
	}
	# Logging end
}
