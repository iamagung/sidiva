<?php

namespace App\Http\Controllers\HC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenagaMedis;
use App\Models\LayananHC;
use App\Models\Dokter;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class TenagaMedisController extends Controller
{
    function __construct()
	{
		$this->title = 'Tenaga Medis';
	}

    public function main()
    {
        if(request()->ajax()){
            $data = TenagaMedis::orderBy('id_tenaga_medis','DESC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					$txt = "
                      <button class='btn btn-sm btn-primary' title='Edit' onclick='formAdd(`$row->id_tenaga_medis`)'><i class='fadeIn animated bx bxs-file' aria-hidden='true'></i></button>
                      <button class='btn btn-sm btn-danger' title='Delete' onclick='hapus(`$row->id_tenaga_medis`)'><i class='fadeIn animated bx bxs-trash' aria-hidden='true'></i></button>
					";
					return $txt;
				})
                ->addColumn('jenis_layanan', function($row){
					if (!empty($row->layanan_id)) {
                        $text = LayananHC::where('id_layanan_hc', $row->layanan_id)->first()->nama_layanan;
                    } else {
                        $text = '-';
                    }
                    return $text;
				})
                ->addColumn('namaTM', function($row){
					if (!empty($row->kode_dokter)) {
                        $text = DB::connection('dbwahidin')->table('tm_setupall')->where('groups','Dokter')
                            ->where('setupall_id', $row->kode_dokter)->first()->nilaichar;
                    } else {
                        $text = '-';
                    }
                    return $text;
				})
				->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.homecare.tenagamedis.main', $data);
    }

    public function form(Request $request)
    {
        if (empty($request->id)) {
            $data['tenaga_medis'] = '';
            $data['title'] = "Tambah ".$this->title;
		}else{
			$data['tenaga_medis'] = TenagaMedis::where('id_tenaga_medis', $request->id)->first();
            $data['title'] = "Edit ".$this->title;
		}
        $data['layanan'] = LayananHC::all();
        # Start Get Dokter
        $data['getTenagaMedis'] = TenagaMedis::all();
        $arrDokter = [];
		foreach ($data['getTenagaMedis'] as $key => $v) {
			$getKdDokter = $v->kode_dokter;
			array_push($arrDokter, $getKdDokter);
		}
        $data['dokter'] = DB::connection('dbwahidin')->table('tm_setupall')->whereNotIn('setupall_id', $arrDokter)
            ->where('groups','Dokter')->where('nilaichar', '!=', '')->get();
        # End Get Dokter
        $content = view('admin.homecare.tenagamedis.form', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    public function store(Request $request)
    {
        $rules = array(
            'kode_dokter' => 'required',
            // 'telepon' => 'required',
            'layanan_id' => 'required',
        );
        $messages = array(
            'required'  => 'Kolom Harus Diisi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        } else {
            try {
                if (empty($request->id)) {
                    $data = new TenagaMedis;
                } else {
                    $data = TenagaMedis::where('id_tenaga_medis', $request->id)->first();
                }
                $data->kode_dokter = $request->kode_dokter;
                // $data->telepon     = $request->telepon;
                $data->layanan_id  = $request->layanan_id;
                $data->status      = ($request->is_melayani == 'on') ? 'MELAYANI' : 'TIDAK MELAYANI';
                $data->save();

                if ($data) {
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

    public function delete(Request $request)
    {
        try {
            $data = TenagaMedis::where('id_tenaga_medis', $request->id)->first();
            $data->delete();
    
            if ($data) {
                $return = ['type' => 'success', 'status' => 'success', 'code' => '200'];
            } else {
                $return = ['type' => 'success', 'status' => 'success', 'code' => '201'];
            }
            return $return;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR DELETE LAYANAN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
