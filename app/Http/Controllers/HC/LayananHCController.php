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
            $data = LayananHC::orderBy('id_layanan_hc','DESC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					$txt = "
                    <button class='btn' title='Edit' onclick='formAdd(`$row->id_layanan_hc`)'><i class='fadeIn animated bx bxs-edit' style='color: blue;' aria-hidden='true'></i></button>
                    <button class='btn' title='Delete' onclick='hapus(`$row->id_layanan_hc`)'><i class='fadeIn animated bx bxs-trash' style='color: red;' aria-hidden='true'></i></button>
					";
					return $txt;
				})
                ->addColumn('formatHarga', function($row){
					return $format = "Rp.".number_format($row->harga,0,',','.');
				})
                ->addColumn('desc', function($row){
					return $row->deskripsi;
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
        $rules = array(
            'nama_layanan' => 'required',
            'jenis_layanan' => 'required',
            'harga' => 'required',
            'jumlah_hari' => 'required',
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
                    $data = new LayananHC;
                } else {
                    $data = LayananHC::where('id_layanan_hc', $request->id)->first();
                }
                $data->jenis_layanan    = $request->jenis_layanan;
                $data->nama_layanan       = $request->nama_layanan;
                $data->harga            = preg_replace("/[^0-9]/", "", $request->harga);
                $data->jumlah_hari      = $request->jumlah_hari;
                $data->deskripsi        = $request->deskripsi;
                $data->save();

                if ($data) {
                    $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
                } else {
                    $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
                }
                return $return;
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR SIMPAN LAYANAN('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
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
            $log = ['ERROR DELETE LAYANAN HC ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
