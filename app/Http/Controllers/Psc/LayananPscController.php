<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use App\Models\LayananAmbulance;
use Illuminate\Http\Request;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth, Hash;
// use App\Models\Pengguna;

class LayananPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Layanan Ambulance';
	}

    public function main() {
        if(request()->ajax()){
            // return 'return';
            $data = LayananAmbulance::orderBy('id_layanan_ambulance','DESC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					$txt = "
                        <button class='btn btn-sm' title='Edit' onclick='formAdd(`$row->id_layanan_ambulance`)'><i class='bx bxs-edit' style='color:#0000ff; font-size: 30px'></i></button>
                        <button class='btn btn-sm' title='Delete' onclick='hapus(`$row->id_layanan_ambulance`)'><i class='bx bxs-trash' style='color:#ff0000; font-size: 30px'></i></button>
					";
					return $txt;
				})
				->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.psc.layanan.main', $data);
    }

    public function form(Request $request)
    {
        if (empty($request->id)) {
            $data['data'] = '';    
		}else{
            $data['data'] = LayananAmbulance::where('id_layanan_ambulance',$request->id)->first();
		}
        $content = view('admin.psc.layanan.modal', $data)->render();
		return ['content'=>$content];
    }

    public function store(Request $request)
    {
        try {
            if (!empty($request->id)) {
                $data = LayananAmbulance::where('id_layanan_ambulance', $request->id)->first();
            } else {
                $data = new LayananAmbulance;
            }
            $data->nama_layanan = $request->nama_layanan;
            $data->status = 'active';
            $data->save();

            if ($data) {
                return ['code' => 200, 'status' => 'success', 'message' => 'Berhasil Ditambahkan.'];
            }else{
                return ['code' => 201, 'status' => 'error', 'message' => 'Gagal Ditambahkan.'];
            }
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SIMPAN USER TM ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    
    public function delete(Request $request)
    {
        try {
            $data = LayananAmbulance::where('id_layanan_ambulance', $request->id)->first();
            $data->delete();
    
            if ($data) {
                $return = ['type' => 'success', 'status' => 'success', 'code' => '200', 'message' => 'Berhasil Dihapus'];
            } else {
                $return = ['type' => 'error', 'status' => 'error', 'code' => '201', 'message' => 'Gagal Dihapus'];
            }
            return $return;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR DELETE USER TM ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
