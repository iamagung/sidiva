<?php

namespace App\Http\Controllers\HC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LayananHC;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class LayananHCController extends Controller
{
    function __construct()
	{
		$this->title = 'Jenis Layanan';
	}

    public function main(Request $request)
    {
        if(request()->ajax()){
            // return 'return';
            $data = LayananHC::orderBy('id_layanan_hc','DESC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					$txt = "
                      <button class='btn btn-sm btn-primary' title='Edit' onclick='formAdd(`$row->id_layanan_hc`)'><i class='fadeIn animated bx bxs-file' aria-hidden='true'></i></button>
                      <button class='btn btn-sm btn-danger' title='Delete' onclick='hapus(`$row->id_layanan_hc`)'><i class='fadeIn animated bx bxs-trash' aria-hidden='true'></i></button>
					";
					return $txt;
				})
				->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.homecare.layanan.main', $data);
    }

    public function form(Request $request)
    {
        if (empty($request->id)) {
            $data['layanan'] = '';
            $data['title'] = "Tambah ".$this->title;
		}else{
			$data['layanan'] = LayananHC::where('id_layanan_hc', $request->id)->first();
            $data['title'] = "Edit ".$this->title;
		}
        $content = view('admin.homecare.layanan.form', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    public function store(Request $request)
    {
        // return $request->all();
        try {
            if (empty($request->id)) {
                $data = new LayananHC;
            } else {
                $data = LayananHC::where('id_layanan_hc', $request->id)->first();
            }
            $data->nama_layanan       = $request->nama_layanan;
            $data->save();

            if ($data) {
                $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
            } else {
                $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
            }
            return $return;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SIMPAN LAYANAN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = LayananHC::where('id_layanan_hc', $request->id)->first();
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
    // function index()
    // {
    //     $data['title'] = $this->title;
    //     return view('admin.homecare.layanan.main', $data);
    // }
    // function fetch_data(Request $request)
    // {
    //     if($request->ajax())
    //     {
    //         $data = DB::table('layanan_hc')->orderBy('id_layanan_hc','desc')->get();
    //         echo json_encode($data);
    //     }
    // }
    // function add_data(Request $request)
    // {
    //     if($request->ajax())
    //     {
    //         $data = array(
    //             'nama_layanan'    =>  $request->nama_layanan
    //         );
    //         $id = DB::table('layanan_hc')->insert($data);
    //         if($id > 0)
    //         {
    //             echo '<div class="alert alert-success">Data Berhasil Ditambahkan</div>';
    //         }
    //     }
    // }
    // function update_data(Request $request)
    // {
    //     if($request->ajax())
    //     {
    //         $data = array(
    //             $request->column_name  =>  $request->column_value
    //         );
    //         DB::table('layanan_hc')
    //             ->where('id_layanan_hc', $request->id)
    //             ->update($data);
    //         echo '<div class="alert alert-success">Data Berhasil Di Update</div>';
    //     }
    // }
    // function delete_data(Request $request)
    // {
    //     if($request->ajax())
    //     {
    //         DB::table('layanan_hc')
    //             ->where('id_layanan_hc', $request->id)
    //             ->delete();
    //         echo '<div class="alert alert-success">Data Berhasil Di Hapus</div>';
    //     }
    // }
}
