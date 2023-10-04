<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverAmbulance;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class DriverPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Driver Ambulance';
	}

    public function main(Request $request) {
        if(request()->ajax()){
            // return 'return';
            $data = DriverAmbulance::orderBy('id_driver','DESC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					$txt = "
                        <button class='btn btn-sm btn-secondary' title='Edit' onclick='formAdd(`$row->id_driver`)'><i class='bx bxs-edit' style='color:white;' aria-hidden='true'></i></button>
                        <button class='btn btn-sm btn-danger' title='Delete' onclick='hapus(`$row->id_driver`)'><i class='fadeIn animated bx bxs-trash' aria-hidden='true'></i></button>
					";
					return $txt;
				})
				->rawColumns(['actions'])
				->toJson();
		}

        $data['title'] = $this->title;
        return view('admin.psc.driver.main', $data);
    }

    public function form(Request $request)
    {
        if (empty($request->id)) {
            $data['data'] = '';    
		}else{
            $data['data'] = DriverAmbulance::where('id_driver',$request->id)->first();
		}
        $content = view('admin.psc.driver.modal', $data)->render();
		return ['content'=>$content];
    }

    public function store(Request $request)
    {
        // return $request->all();
        try {
            if (empty($request->id)) {
                $data = new DriverAmbulance;
            } else {
                $data = DriverAmbulance::where('id_driver', $request->id)->first();
            }
            $data->telepon = $request->telepon;
            $data->nama_driver = $request->nama_driver;
            $data->alamat = $request->alamat;
            $data->save();

            if ($data) {
                $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
            } else {
                $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
            }
            return $return;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SIMPAN DRIVER ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = DriverAmbulance::where('id_driver', $request->id)->first();
            $data->delete();
    
            if ($data) {
                $return = ['type' => 'success', 'status' => 'success', 'code' => '200'];
            } else {
                $return = ['type' => 'success', 'status' => 'success', 'code' => '201'];
            }
            return $return;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR DELETE DRIVER ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
