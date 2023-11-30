<?php

namespace App\Http\Controllers\HC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanHC;
use App\Models\LayananHC;
use App\Models\LayananPermintaanHc;
use App\Models\TenagaMedisHomecare;
use App\Models\TenagaMedisPermintaanHomecare;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class PermintaanHCController extends Controller
{
    function __construct() {
		$this->title = 'Permintaan Homecare';
	}

    public function main(Request $request) {
        if(request()->ajax()){
            // return $request;
            if (!empty($request->tanggal) && !empty($request->status)) {
                if ($request->status=='all') {
                    $data = PermintaanHC::where('tanggal_kunjungan', $request->tanggal)
                    ->whereIn('status_pasien', ['belum','proses','menunggu','tolak','batal','selesai'])
                    // ->with('resep_obat')
                    ->orderBy('created_at','ASC')->get();
                } else {
                    $data = PermintaanHC::where('tanggal_kunjungan', $request->tanggal)
                    ->where('status_pasien', $request->status)
                    // ->with('resep_obat')
                    ->orderBy('created_at','ASC')->get();
                }
            } else {
                $data = PermintaanHC::where('tanggal_kunjungan', $request->tanggal)
                // ->with('resep_obat')
                ->orderBy('created_at','ASC')->get();
            }
            foreach ($data as $k => $v) {
                $layanan = LayananPermintaanHc::where('permintaan_id', $v->id_permintaan_hc)->get();
                foreach ($layanan as $key => $val) {
                    $namaLayanan = LayananHC::where('id_layanan_hc', $val->layanan_id)->first();
                    if (!empty($namaLayanan)) {
                        if(!isset($v->listLayanan)){
                            $v->listLayanan = $namaLayanan->nama_layanan;
                        }else{
                            $v->listLayanan .= ", " . $namaLayanan->nama_layanan;
                        }
                    }
                }
            }
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('opsi', function($row){
                    // if ($row->tanggal_kunjungan!=date('Y-m-d')&&$row->status_pasien=='belum'&&$row->status_pembayaran=='pending') {
                    //     return "
                    //     <button class='btn btn-sm btn-primary' title='terima' onclick='terima(`$row->id_permintaan_hc`)'>Terima</button>
                    //     <button class='btn btn-sm btn-danger' title='tolak' onclick='tolak(`$row->id_permintaan_hc`)'>Tolak</button>
                    //     ";
                    // } else if($row->tanggal_kunjungan==date('Y-m-d')){
                    //     if ($row->status_pasien=='belum') {
                    //         return "
                    //         <button class='btn btn-sm btn-primary disabled' title='terima'>Terima</button>
                    //         <button class='btn btn-sm btn-danger disabled' title='tolak'>Tolak</button>
                    //         ";
                    //     } else if($row->status_pembayaran=='paid') {
                    //         if($row->status_pasien=='menunggu') {
                    //             return "<button class='btn btn-sm btn-success' title='pilih nakes' onclick='pilih(`$row->id_permintaan_hc`)'>PILIH NAKES</button>";
                    //         } else {
                    //             if($row->resep_obat == '') {
                    //                 return "<button class='btn btn-sm btn-secondary' title='detail' onclick='detail(`$row->id_permintaan_hc`)'>DETAIL</button><button class='btn btn-sm btn-primary' title='eresep' onclick='detailEresep(`$row->id_permintaan_telemedicine`)'>Eresep</button>";

                    //             } else {
                    //                 return "<button class='btn btn-sm btn-secondary' title='detail' onclick='detail(`$row->id_permintaan_hc`)'>DETAIL</button>";
                    //             }
                    //         }
                    //     }
                    // } else {
                    //     // if($row->resep_obat == '') {
                    //     //     return '<div class="text-center">-</div>';
                    //     // } else {
                    //     //     return "<button class='btn btn-sm btn-primary' title='eresep' onclick='detailEresep(`$row->id_permintaan_hc`)'>Eresep</button>";
                    //     // }
                    //     return '<div class="text-center">-</div>';
                    // }
                    return "<button class='btn btn-sm btn-success' title='pilih nakes' onclick='pilih(`$row->id_permintaan_hc`)'>PILIH NAKES</button>
                    <button class='btn btn-sm btn-secondary' title='detail' onclick='detail(`$row->id_permintaan_hc`)'>DETAIL</button>";
				})
                ->addColumn('layanan', function($row){
                    $txt = !empty($row->listLayanan)?$row->listLayanan:'-';
					return $txt;
				})
                ->addColumn('lokasi', function($row){
                    $distance = $this->calculateDistance($row->latitude, $row->longitude);
                    $txt = $row->latitude.",".$row->longitude." ($distance)";
					return $txt;
				})
                ->addColumn('noRm', function($row){
					return !empty($row->no_rm)?$row->no_rm:'-';
				})
                ->addColumn('pembayaran', function($row){
					return $row->status_pembayaran=='pending'?'BELUM BAYAR':'LUNAS';
				})
                ->addColumn('status', function($row){
					if ($row->status_pasien=='proses') {
                        return 'PROSES';
                    } else if ($row->status_pasien=='menunggu') {
                        return 'MENUNGGU';
                    } else if ($row->status_pasien=='batal') {
                        return 'DIBATALKAN';
                    } else if ($row->status_pasien=='tolak') {
                        return 'DITOLAK';
                    } else if ($row->status_pasien=='selesai') {
                        return 'SELESAI';
                    } else {
                        return '-';
                    }
				})
				->rawColumns(['opsi'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.homecare.permintaan.main', $data);
    }

    public function form(Request $request) {
        $data['title'] = "Pilih Tenaga Medis Home Care";
        $data['view'] = isset($request->view)==1?1:0;
        if (empty($request->id)) {
            return ['code' => 500, 'type' => 'error', 'status' => 'error', 'message' => 'Terjadi kesalahan.'];
		}
        $data['dtLayanan'] = LayananHC::all();
        $data['permintaan'] = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
        $getLayanan = LayananPermintaanHc::select('layanan_id')
            ->where('permintaan_id',$data['permintaan']->id_permintaan_hc)
            ->get();
        $data['setLayanan'] = collect($getLayanan)->map(function($dt){
            return $dt->layanan_id;
        })->toArray();
        $getNakes = TenagaMedisPermintaanHomecare::select('tenaga_medis_id')->where('permintaan_hc_id',$data['permintaan']->id_permintaan_hc)->get();
        $data['setNakes'] = collect($getNakes)->map(function($dt){
            return $dt->tenaga_medis_id;
        })->toArray();
        $data['nakes'] = TenagaMedisHomecare::leftJoin(DB::connection('dbranap')->raw('wahidin_ranap.users as u'),'u.id','=','tenaga_medis_homecare.nakes_id')->get();
        $content = view('admin.homecare.permintaan.modal', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    // public function formEresep(Request $request)
    // {
    //     $rules = array(
    //         'id' => 'required',
    //     );
    //     $messages = array(
    //         'required'  => 'Kolom Harus Diisi',
    //     );
    //     $valid = Validator::make($request->all(), $rules,$messages);
    //     if($valid->fails()) {
    //         return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
    //     } else {
    //         $permintaan = PermintaanHC::where('id_permintaan_hc', $request->id)
    //             ->with('resep_obat', function($q) {
    //                 $q->with('resep_obat_detail');
    //             })
    //             ->with('payment_permintaan_eresep')
    //             ->first();

    //         if($permintaan) {
    //             $data['permintaan'] = $permintaan;
    //             $content = view('admin.homecare.permintaan.modalEresep', $data)->render();
    //             return ['status' => 'success', 'content' => $content, 'data' => $data];
    //         }
    //     }
    //     return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data permintaan tidak ditemukan'];
    // }

    public function tolak(Request $request) {
        try {
            $data = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
            $data->status_pasien         = 'tolak';
            $data->save();

            if ($data) {
                return ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Pasien Berhasil Ditolak'];
            } else {
                return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Pasien Gagal Ditolak'];
            }
        } catch (\Throwable $e) {
            $log = ['ERROR TOLAK PERMINTAAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function terima(Request $request) {
        try {
            $data = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
            $data->status_pasien         = 'menunggu';
            $data->save();

            if ($data) {
                return ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Berhasil.'];
            } else {
                return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Gagal.'];
            }
        } catch (\Throwable $e) {
            $log = ['ERROR TERIMA PERMINTAAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function save(Request $request)
    {
        // return $request->all();
        $rules = array(
            'tenaga_medis_id' => 'required'
        );
        $messages = array(
            'tenaga_medis_id.required' => 'Tenaga medis wajib diisi'
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        } else {
            DB::beginTransaction();
            try {
                # Update permintaan homecare
                $data = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
                $data->status_pasien = 'proses';
                if (empty($data->no_rm)) {
                    $data->no_rm = Help::generateRM();
                }
                $data->save();
                if (!$data) {
                    DB::rollback();
                    return ['code'=>400, 'type'=>'error', 'status'=>'error', 'message'=>'Gagal update permintaan homecare'];
                }
                # Insert to tm_customer
                $dtCustomer = [
                    'KodeCust' => $data->no_rm,
                    'Tempat'   => $data->tempat_lahir,
                    'NoKtp'    => $data->nik,
                    'NamaCust' => $data->nama,
                    'TglLahir' => $data->tanggal_lahir,
                    'Alamat'   => $data->alamat,
                    'jenisKel' => $data->jenis_kelamin,
                    'Telp'     => $data->no_telepon,
                ];
                $insertCustomer = DB::connection('dbrsud')->table('tm_customer')->insert($dtCustomer);
                if (!$insertCustomer) {
                    DB::rollback();
                    return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Gagal simpan customer'];
                }
                # Insert tenaga medis permintaan homecare
                foreach ($request->tenaga_medis_id as $medis) {
                    $tenagaMedis = new TenagaMedisPermintaanHomecare;
                    $tenagaMedis->tenaga_medis_id = $medis;
                    $tenagaMedis->permintaan_hc_id = $request->id;
                    $tenagaMedis->save();
                    if (!$tenagaMedis) {
                        DB::rollback();
                        return ['code'=>400, 'type'=>'error', 'status'=>'error', 'message'=>'gagal update tenaga medis homecare'];
                    }
                }
                DB::commit();
                return ['code' => 200, 'type' => 'success', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
            } catch (\Throwable $e) {
                DB::rollback();
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
