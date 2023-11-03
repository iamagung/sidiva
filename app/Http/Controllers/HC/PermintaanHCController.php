<?php

namespace App\Http\Controllers\HC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanHC;
use App\Models\PaketHC;
use App\Models\LayananHC;
use App\Models\TenagaMedisHomecare;
use App\Models\TenagaMedisPermintaanHomecare;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class PermintaanHCController extends Controller
{
    function __construct() {
		$this->title = 'Permintaan Baru';
	}

    public function main(Request $request) {
        if(request()->ajax()){
            if (!empty($request->tanggal) && !empty($request->status)) {
                if ($request->status=='all') {
                    $data = PermintaanHC::where('tanggal_kunjungan', $request->tanggal)
                    ->whereIn('status_pasien', ['belum','proses','menunggu','ditolak','batal','selesai'])
                    ->orderBy('created_at','ASC')->get();
                } else {
                    $data = PermintaanHC::where('tanggal_kunjungan', $request->tanggal)
                    ->where('status_pasien', $request->status)
                    ->orderBy('created_at','ASC')->get();
                }
            } else {
                $data = PermintaanHC::whereIn('status_pasien', ['belum','proses','menunggu'])
                ->where('tanggal_kunjungan', $request->tanggal)
                ->orderBy('created_at','ASC')->get();
            }
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('opsi', function($row){
					if ($row->status_pasien == 'belum') {
                        return "
                        <button class='btn btn-sm btn-primary' title='terima' onclick='terima(`$row->id_permintaan_hc`)'>Terima</button>
                        <button class='btn btn-sm btn-danger' title='tolak' onclick='tolak(`$row->id_permintaan_hc`)'>Tolak</button>
                        ";
                    } else if($row->status_pasien == 'menunggu') {
                        return "<button class='btn btn-sm btn-success' title='pilih nakes' onclick='pilih(`$row->id_permintaan_hc`)'>PILIH NAKES</button>";
                    } else {
                        return "<button class='btn btn-sm btn-secondary' title='detail' onclick='detail(`$row->id_permintaan_hc`)'>DETAIL</button>";
                    }
				})
                ->addColumn('layanan', function($row){
					return LayananHC::where('id_layanan_hc', $row->layanan_hc_id)->first()->nama_layanan;
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
                    } else if ($row->status_pasien=='ditolak') {
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
        $arrayNakes = [];
        $data['view'] = isset($request->view)==1?1:0;
        if (empty($request->id)) {
            return ['code' => 500, 'type' => 'error', 'status' => 'error', 'message' => 'Terjadi kesalahan.'];
		}
        $data['dtLayanan'] = LayananHC::all();
        $data['permintaan'] = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
        $data['layanan']    = LayananHC::where('id_layanan_hc', $data['permintaan']->layanan_hc_id)->first();
        $getMedis = TenagaMedisPermintaanHomecare::select('tenaga_medis_id')->where('permintaan_hc_id',$data['permintaan']->id_permintaan_hc)->get();
        foreach ($getMedis as $key => $value) {
            array_push($arrayNakes,$value->tenaga_medis_id);
        }
        $data['selectedNakes'] = $arrayNakes;
        $data['nakes'] = TenagaMedisHomecare::leftJoin(DB::connection('dbranap')->raw('wahidin_ranap.users as u'),'u.id','=','tenaga_medis_homecare.nakes_id')->get();
        $content = view('admin.homecare.permintaan.modal', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }
    public function tolak(Request $request) {
        try {
            $data = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
            $data->status_pasien         = 'ditolak';
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
            'tenaga_medis_id' => 'required',
            'layanan_id' => 'required'
        );
        $messages = array(
            'required'  => 'Kolom Harus Diisi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        } else {
            try {
                DB::beginTransaction();
                $data = PermintaanHC::where('id_permintaan_hc', $request->id)->first();
                $data->tenaga_medis_id       = implode(';', $request->tenaga_medis_id ?? []);;
                $data->status_pasien         = 'proses';
                $data->save();
                if (!$data) {
                    DB::rollback();
                    return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
                }
                # Start save to tm_customer
                if (empty($data->no_rm)) {
                    $noRm = Help::generateRM();
                    $data->no_rm = $noRm;
                    $data->save(); # Update no rm ke table permintaan hc

                    $dtCustomer = [
                        'KodeCust' => $noRm,
                        'Tempat'   => $data->tempat_lahir,
                        'NoKtp'    => $data->nik,
                        'NamaCust' => $data->nama,
                        'TglLahir' => $data->tanggal_lahir,
                        'Alamat'   => $data->alamat,
                        'jenisKel' => $data->jenis_kelamin,
                        'Telp'     => $data->telepon,
                    ];
                    $insertCustomer = DB::connection('dbrsud')->table('tm_customer')->insert($dtCustomer);
                    if (!$insertCustomer) {
                        DB::rollback();
                        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
                    }
                }
                # End save to tm_customer
                # Start save to tenaga_medis_permintaan_homecare
                $i = 0;
                if ($request->tenaga_medis_id) {
                    foreach ($request->tenaga_medis_id as $key => $value) {
                        $tenagaMedis = new TenagaMedisPermintaanHomecare;
                        $tenagaMedis->tenaga_medis_id = $request->tenaga_medis_id[$i];
                        $tenagaMedis->permintaan_hc_id = $data->id_permintaan_hc;
                        $tenagaMedis->save();
                        $i++;
                        if (!$tenagaMedis) {
                            DB::rollback();
                            return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
                        }
                    }
                }
                # End save to tenaga_medis_permintaan_homecare
                DB::commit();
                return ['code' => 200, 'type' => 'success', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
            } catch (\Throwable $e) {
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
