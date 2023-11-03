<?php

namespace App\Http\Controllers\HC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenagaMedisHomecare;
use App\Models\LayananHC;
use App\Models\Dokter;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class TenagaMedisController extends Controller
{
    function __construct() {
		$this->title = 'Tenaga Medis Homecare';
	}

    public function main() {
        if(request()->ajax()){
            $data = TenagaMedisHomecare::orderBy('id_tenaga_medis','ASC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					$txt = "
                      <button class='btn btn-sm btn-primary' title='Edit' onclick='formAdd(`$row->id_tenaga_medis`)'><i class='fadeIn animated bx bxs-file' aria-hidden='true'></i></button>
                      <button class='btn btn-sm btn-danger' title='Delete' onclick='hapus(`$row->id_tenaga_medis`)'><i class='fadeIn animated bx bxs-trash' aria-hidden='true'></i></button>
					";
					return $txt;
				})
                ->addColumn('layanan', function($row){
                    return LayananHC::where('id_layanan_hc', $row->layanan_id)->first()->nama_layanan;
				})
                ->addColumn('nama_nakes', function($row){
                    return DB::connection('dbranap')->table('users')->where('id',$row->nakes_id)->first()->name;
				})
                ->addColumn('stts', function($row){
                    return $row->status==false?'TIDAK MELAYANI':'MELAYANI';
				})
				->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.homecare.tenagamedis.main', $data);
    }

    public function getNakes(Request $request) {
        $data = DB::connection('dbranap')->table('users')
            ->where('level_user', $request->jenis)
            ->where('is_active',1)
            ->get();
        if (count($data)>0) {
            return Help::custom_response(200, "success", 'Ok', $data);
        }
        return Help::custom_response(500, "error", 'Data not found', null);
    }
    public function form(Request $request)
    {
        if (empty($request->id)) {
            $data['title'] = "Tambah";
            $data['data'] = '';
            $data['nakes'] = '';
		}else{
            $data['title'] = "Edit";
			$data['data'] = TenagaMedisHomecare::where('id_tenaga_medis', $request->id)->first();
            $data['nakes'] = $data['data']->nakes_id;
		}
        $data['layanan'] = LayananHC::all();
        $content = view('admin.homecare.tenagamedis.modal', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    public function store(Request $request)
    {
        try {
            if (empty($request->id)) {
                $data = new TenagaMedisHomecare;
                $data->status = false;
            } else {
                $data = TenagaMedisHomecare::where('id_tenaga_medis', $request->id)->first();
            }
            $data->jenis_nakes  = $request->jenis_nakes;
            $data->nakes_id     = $request->nama_nakes;
            $data->layanan_id   = $request->layanan_id;
            $data->save();

            if ($data) {
                return ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
            }
            return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
        } catch (\Throwable $e) {
            $log = ['ERROR SIMPAN TENAGA MEDIS ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = TenagaMedisHomecare::where('id_tenaga_medis', $request->id)->first();
            $data->delete();
    
            if ($data) {
                return ['type' => 'success', 'status' => 'success', 'code' => '200'];
            } 
            return ['type' => 'success', 'status' => 'success', 'code' => '201'];
        } catch (\Throwable $e) {
            $log = ['ERROR DELETE LAYANAN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
