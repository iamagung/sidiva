<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SyaratAmbulance;
use App\Models\PermintaanAmbulance;
use App\Models\PengaturanAmbulance;
use App\Helpers\Helpers as Help;
use Validator, DB, Auth, Hash;

class ApiPendaftaranAmbulanceController extends Controller
{
    // public function getSyaratAturanHC(Request $request)
    // {
    //     try{
    //         $data = SyaratHC::where('id_syarat_hc', 1)->first();
    //         if (!empty($data)) {
    //             $respon = [
    //                 'metaData' => [
    //                     "code" => 200,
    //                     "message" => 'Berhasil'
    //                 ],
    //                 'response' => $data
    //             ];
    //         } else {
    //             $respon = [
    //                 'metaData' => [
    //                     "code" => 500,
    //                     "message" => 'Data Tidak Ditemukan'
    //                 ],
    //                 'response' => []
    //             ];
    //         }

    //         return response()->json($respon);
    //     } catch (\Throwable $e) {
    //         # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
    //         $log = ['ERROR GET SYARAT & ATURAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
    //         Help::logging($log);

    //         return Help::resApi('Terjadi kesalahan sistem',500);
    //     }
    // }
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
            $tanggal = date('Y-m-d');
            $checkPendaftaran = $this->checkPendaftaran($request->nik, $tanggal);
            if ($checkPendaftaran > 0) {
                return Help::resAjax(['message'=>'NIK telah digunakan untuk mendaftar pada tanggal '.$this->tgl_indo($tanggal),'code'=>500]);
            }
            if (strlen($request->nik)!=16) {
                return Help::resAjax(['message'=>'NIK tidak sesuai standar 16 digit','code'=>500]);
            }
            try {
                $data = new PermintaanAmbulance;
                $data->nik                  = $request->nik;
                $data->nama                 = strtoupper($request->nama);
                $data->tanggal_kunjungan    = $tanggal;
                $data->tempat_lahir         = $request->tempat_lahir;
                $data->tanggal_lahir        = $request->tanggal_lahir;
                $data->jenis_kelamin        = $request->jenis_kelamin;
                $data->alamat               = $request->alamat;
                $data->no_telepon           = $request->telepon;
                $data->latitude_jemput      = !empty($request->latitude_jemput)?$request->latitude_jemput:NULL;
                $data->longitude_jemput     = !empty($request->longitude_jemput)?$request->longitude_jemput:NULL;
                $data->latitude_antar       = !empty($request->latitude_antar)?$request->latitude_antar:NULL;
                $data->longitude_antar      = !empty($request->longitude_antar)?$request->longitude_antar:$request->longitude_antar;
                $data->jenis_layanan        = $request->jenis_layanan;
                $data->keterangan           = !empty($request->keterangan)?$request->keterangan:NULL;
                $data->jenis_pembayaran     = $request->jenis_pembayaran;
                $data->status_pembayaran    = 'pending';
                // $data->biaya_ke_lokasi      = !empty($pasien) ? $pasien->Telp : $request->no_telepon;
                $data->status_pasien        = 'belum';
                $data->save();

                if ($data) {
                    # Update Biaya layanan & biaya ke lokasi
                    // $biayaPerKm = PengaturanAmbulance::where('id_pengaturan_ambulance', 1)->first()->biaya_per_km;
                    // $distance   = Help::calculateDistance($data->latitude, $data->longitude);
                    // $upData     = PermintaanHC::where('id_permintaan_hc', $data->id_permintaan_hc)->first();
                    // $upData->biaya_layanan   = PaketHC::where('id_paket_hc', $data->paket_hc_id)->first()->harga;
                    // $upData->biaya_ke_lokasi = (int)$biayaPerKm * (int)$distance;
                    // $upData->save();
                    return Help::resAjax(['message'=>'Berhasil Mendaftar','code'=>200]);
                }else{
                    return Help::resAjax(['message'=>'Gagal Mendaftar','code'=>500]);
                }
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR PENDAFTARAN AMBULANCE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 500,
                ],
                'response' => [],
            ]);
        }
    }
    public function checkPendaftaran($nik, $tanggal) //check nik apakah sudah digunakan
    {
        $check = PermintaanAmbulance::where('nik','=',$nik)->where('tanggal_kunjungan','=',$tanggal)->count();
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
