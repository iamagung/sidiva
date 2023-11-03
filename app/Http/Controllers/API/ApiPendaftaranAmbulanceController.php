<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SyaratAmbulance;
use App\Models\LayananAmbulance;
use App\Models\PermintaanAmbulance;
use App\Models\PengaturanAmbulance;
use App\Helpers\Helpers as Help;
use Validator, DB, Auth, Hash;

class ApiPendaftaranAmbulanceController extends Controller
{
    public function getSyaratAturanAmbulance(Request $request)
    {
        try{
            $data = SyaratAmbulance::where('id_syarat_aturan_ambulance', 1)->first();
            if (!empty($data)) {
                return Help::custom_response(200, "success", "OK", $data);
            } else {
                return Help::custom_response(500, "error", "Data not found.", $data);
            }
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET SYARAT & ATURAN AMBULANCE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function getLayananAmbulance(Request $requesrt){
        try{
            $data = LayananAmbulance::all();
            if (count($data)>0) {
                return Help::custom_response(200, "success", "OK", $data);
            } else {
                return Help::custom_response(500, "error", "Data not found", $data);
            }
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET LAYANAN AMBULANCE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function pesanJadwalAmbulance(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'nik' => 'required',
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'telepon' => 'required',
            'latitude_jemput' => 'required',
            'longitude_jemput' => 'required',
            'latitude_antar' => 'required',
            'longitude_antar' => 'required',
            'jenis_layanan' => 'required',
            'jenis_pembayaran' => 'required',
        ],[
            'nik.required' => 'NIK Wajib Diisi',
            'nama.required' => 'Nama Lengkap Wajib Di isi',
            'tempat_lahir.required' => 'Tempat Lahir Wajib Di isi',
            'tanggal_lahir.required' => 'Tanggal Lahir Wajib Di isi',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Di isi',
            'alamat.required' => 'Alamat Wajib Di isi',
            'telepon.required' => 'Telepon Wajib Di isi',
            'jenis_layanan.required' => 'Jenis Layanan Wajib Di isi',
            'jenis_pembayaran.required' => 'Jenis Pembayaran Wajib Di isi',
            'latitude_jemput.required' => 'Lokasi Wajib Diisi',
            'longitude_jemput.required' => 'Lokasi Wajib Diisi',
            'latitude_antar.required' => 'Lokasi Wajib Diisi',
            'longitude_antar.required' => 'Lokasi Wajib Diisi'
        ]);
        if (!$validate->fails()) {
            date_default_timezone_set('Asia/Jakarta');
            if (strlen($request->nik)!=16) {
                return Help::resAjax(['message'=>'NIK tidak sesuai standar 16 digit','code'=>500]);
            }
            try {
                $latAntar = $request->latitude_antar;
                $longAntar = $request->longitude_antar;
                $latJemput = $request->latitude_jemput;
                $longJemput = $request->longitude_jemput;
                $data = new PermintaanAmbulance;
                $data->nik                  = $request->nik;
                $data->nama                 = strtoupper($request->nama);
                $data->tanggal_order        = date('Y-m-d');
                $data->tempat_lahir         = $request->tempat_lahir;
                $data->tanggal_lahir        = $request->tanggal_lahir;
                $data->jenis_kelamin        = $request->jenis_kelamin;
                $data->alamat               = $request->alamat;
                $data->no_telepon           = $request->telepon;
                $data->latitude_jemput      = $latJemput;
                $data->longitude_jemput     = $longJemput;
                $data->latitude_antar       = $latAntar;
                $data->longitude_antar      = $longAntar;
                $data->jenis_layanan        = $request->jenis_layanan;
                $data->keterangan           = !empty($request->keterangan)?$request->keterangan:NULL;
                $data->jenis_pembayaran     = $request->jenis_pembayaran;
                $data->status_pembayaran    = 'pending';
                $data->status_pasien        = 'belum';
                # Start hitung biaya ke lokasi
                // $biayaPerKm = PengaturanAmbulance::where('id_pengaturan_ambulance', 1)->first()->biaya_per_km;
                // $distance   = Help::calculateDistance($latAntar,$longAntar,$latJemput,$longJemput);
                // return$data->biaya_ke_lokasi = (int)$biayaPerKm * (int)$distance;
                # End hitung biaya ke lokasi
                $data->save();
                if(!$data){
                    DB::rollback();
                    return Help::resApi('Pendaftaran ambulance gagal.',500);
                }
                return Help::custom_response(200, "success", "Ok.", $data);
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR PENDAFTARAN AMBULANCE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::custom_response(200, "error", $validate->errors()->all()[0], null);
        }
    }
    public function checkPendaftaran($nik, $tanggal) //check nik apakah sudah digunakan
    {
        $check = PermintaanAmbulance::where('nik','=',$nik)->where('tanggal_order','=',$tanggal)->count();
        return $check;
    }
    function tgl_indo($tanggal) { // ubah tanggal menjadi format indonesia
        $bulan = array (
            1 =>   'Januari',
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
        );
        $pecahkan = explode('-', $tanggal);
        return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }
}
