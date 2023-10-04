<?php

namespace App\Http\Controllers\Telemedicine;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanTelemedicine;
use App\Models\PaketTelemedicine;
use App\Models\LayananTelemedicine;
use App\Models\TenagaMedis;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class PermintaanTelemedicineController extends Controller
{
    function __construct()
	{
		$this->title = 'Permintaan Baru';
	}

    public function main(Request $request)
    {
        if(request()->ajax()){
            if (!empty($request->tanggal) && !empty($request->status)) {
                if ($request->status=='all') {
                    $data = PermintaanTelemedicine::where('tanggal_kunjungan', $request->tanggal)
                    ->whereIn('status_pasien', ['belum','proses','batal','selesai'])
                    ->orderBy('created_at','ASC')->get();
                } else {
                    $data = PermintaanTelemedicine::where('tanggal_kunjungan', $request->tanggal)
                    ->where('status_pasien', $request->status)
                    ->orderBy('created_at','ASC')->get();
                }
            } else {
                $data = PermintaanTelemedicine::orderBy('created_at','ASC')->get();
            }
			return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('proses', function($row){
					if ($row->status_pasien == 'belum') {
                        $txt = "
                        <button class='btn btn-sm btn-primary' title='proses' id='pilih' onclick='formAdd(`$row->id_permintaan_telemedicine`)'>Pilih Tenaga Medis</button>
                        ";
                    } else {
                        $txt = "
                        <button class='btn btn-sm btn-primary' title='proses' id='pilih' disabled>Pilih Tenaga Medis</button>
                        ";
                    }
					return $txt;
				})
				->addColumn('opsi', function($row){
					if ($row->status_pasien == 'belum') {
                        $txt = "
                        <button class='btn btn-sm btn-danger' title='proses' onclick='batal(`$row->id_permintaan_telemedicine`)'>Batalkan</button>
                        ";
                    } else {
                        $txt = "
                        <button class='btn btn-sm btn-danger' title='proses' disabled>Batalkan</button>
                        ";
                    }
					return $txt;
				})
                ->addColumn('layanan', function($row){
                    if (!empty($row->layanan_telemedicine_id) && !empty($row->paket_Telemedicine_id)) {
                        $getLayanan = LayananTelemedicine::where('id_layanan_telemedicine', $row->layanan_telemedicine_id)->first()->nama_layanan;
                        $getPaket   = PaketTelemedicine::where('id_paket_telemedicine', $row->paket_telemedicine_id)->first()->nama_paket;
                        $txt        = $getLayanan.' - '.$getPaket;
                    } else {
                        $txt = '';
                    }
					return $txt;
				})
                ->addColumn('lokasi', function($row){
                    if (!empty($row->latitude) && !empty($row->longitude)) {
                        $distance = $this->calculateDistance($row->latitude, $row->longitude);
                        $txt = $row->latitude.",".$row->longitude." ($distance)";
                    } else {
                        $txt = '';
                    }
					return $txt;
				})
                ->addColumn('noRm', function($row){
                    if (!empty($row->no_rm)) {
                        $txt = $row->no_rm;
                    } else {
                        $txt = '-';
                    }
					return $txt;
				})
				->rawColumns(['proses', 'opsi'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.telemedicine.permintaan.main', $data);
    }

    // public function form(Request $request)
    // {
    //     try {
    //         if (empty($request->id)) {
    //             // $data['layanan'] = '';
    //             $permintaan = '';
    //             $layanan    = '';
    //             $paket      = '';
    //         }else{
    //             // $data['layanan'] = PaketHC::where('id_paket_hc', $request->id)->first();
    //             $permintaan = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
    //             $layanan    = LayananHC::where('id_layanan_hc', $permintaan->layanan_hc_id)->first();
    //             $paket      = PaketHC::where('id_paket_hc', $permintaan->paket_hc_id)->first();
    //         }

    //         $getLayanan = LayananHC::all();
    //         $getPaket   = PaketHC::all();
    //         $tenagaMedis = TenagaMedis::all();
    //         $user = DB::connection('dbwahidin')->table('users as u')
    //                 ->rightJoin('login_dokter as ld', 'u.id', 'ld.user_id')->get();
    //         $nama = "";
    //         foreach ($tenagaMedis as $key => $val) {
    //             $kddokter = $val->kode_dokter;
    //             foreach ($user as $k => $v) {
    //                 if ($kddokter == $v->dokter_id) {
    //                     $nama = $v->Nama_Dokter;
    //                 }
    //             }
    //             $val->nama = $nama;
    //             $nama = '';
    //         }

    //         $data = [
    //             'layanan' => $layanan,
    //             'permintaan' => $permintaan,
    //             'paket' => $paket,
    //             'getLayanan' => $getLayanan,
    //             'getPaket' => $getPaket,
    //             'tenagaMedis' => $tenagaMedis
    //         ];
    //         $content = view('admin.homecare.permintaan.modal', $data)->render();
    //         return ['status'=>'success', 'message'=>'Berhasil', 'content'=>$content];
    //     } catch (\Throwable $e) {
    //         # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
    //         $log = ['ERROR PILIH TENAGA MEDIS HC ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
    //         Help::logging($log);

    //         return Help::resApi('Terjadi kesalahan sistem',500);
    //     }
    // }

    public function form(Request $request)
    {
        if (empty($request->id)) {
            $data['layanan'] = '';
            $data['permintaan'] = '';
            $data['layanan'] = '';
            $data['paket']      =  '';
		}else{
			$data['layanan'] = PaketHC::where('id_paket_hc', $request->id)->first();
            $data['permintaan'] = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
            $data['layanan']    = LayananHC::where('id_layanan_hc', $data['permintaan']->layanan_hc_id)->first();
            $data['paket']      = PaketHC::where('id_paket_hc', $data['permintaan']->paket_hc_id)->first();
		}

        $data['getLayanan'] = LayananHC::all();
        $data['getPaket']   = PaketHC::all();
        $data['tenagaMedis'] = TenagaMedis::all();
        $user = DB::connection('dbwahidin')->table('users as u')
                ->rightJoin('login_dokter as ld', 'u.id', 'ld.user_id')->get();
        $nama = "";
        foreach ($data['tenagaMedis'] as $key => $val) {
            $kddokter = $val->kode_dokter;
            foreach ($user as $k => $v) {
                if ($kddokter == $v->dokter_id) {
                    $nama = $v->Nama_Dokter;
                }
            }
            $val->nama = $nama;
            $nama = '';
        }
        $data['title'] = "Pilih Tenaga Telemedicine";
        $content = view('admin.telemedicine.permintaan.form', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    public function save(Request $request)
    {
        $rules = array(
            'tenaga_medis_id' => 'required',
        );
        $messages = array(
            'required'  => 'Kolom Harus Diisi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        } else {
            try {
                $data = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
                $data->tenaga_medis_id       = $request->tenaga_medis_id;
                $data->status_pasien         = 'proses';
                $data->save();

                if ($data) {
                    if ($data->no_rm == null) {
                        $noRm = Help::generateRM();
                        # save to tabel permintaan hc
                        $data->no_rm = $noRm;
                        $data->save();
                        # insert to tabel tm_customer
                        $dtCustomer = [
                            'KodeCust' => $noRm,
                            'NoKtp'    => $data->nik,
                            'NamaCust' => $data->nama,
                            'TglLahir' => $data->tanggal_lahir,
                            'Alamat'   => $data->alamat,
                            'jenisKel' => $data->jenis_kelamin,
                            'Telp'     => $data->telepon,
                        ];
                        DB::connection('dbrsud')->table('tm_customer')->insert($dtCustomer);
                    }
                    $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
                } else {
                    $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
                }
                return $return;
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR SIMPAN TENAGA MEDIS ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }
    }

    public function batal(Request $request)
    {
        try {
            $data = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
            $data->status_pasien         = 'batal';
            $data->save();

            if ($data) {
                $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Pasien Berhasil Dibatalkan'];
            } else {
                $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Pasien Gagal Dibatalkan'];
            }
            return $return;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SIMPAN TENAGA MEDIS ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function calculateDistance($latitude, $longitude)
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
        $return = intval($kilometers).' KM';
        return $return;
    }
}
