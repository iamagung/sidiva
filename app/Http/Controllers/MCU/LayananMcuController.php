<?php

namespace App\Http\Controllers\MCU;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LayananMcu;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class LayananMcuController extends Controller
{
    function __construct() {
		$this->title = 'Layanan MCU';
	}

    public function main() {
        if(request()->ajax()){
            $data = LayananMcu::orderBy('id_layanan','ASC')->get();
			return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('modifyNama', function($row){
					$data = $row->nama_layanan;
					return $txt = "<text>$data</text>";
				})
                ->addColumn('modifyDesc', function($row){
					$data = $row->deskripsi ? (strlen($row->deskripsi) > 10 ? substr($row->deskripsi,0,30).'...' : $row->deskripsi) : '-';
					return $txt = "<text>$data</text>";
				})
                ->addColumn('modifyKategori', function($row){
					$data = "MCU - ".strtoupper($row->kategori_layanan);
					return $txt = "<text>$data</text>";
				})
                ->addColumn('formatHarga', function($row){
					$data = "Rp.".number_format($row->harga,0,',','.');
                    return $txt = "<text>$data</text>";
				})
                ->addColumn('modifyJenis', function($row){
					$data = $row->jenis_layanan;
					return $txt = "<text>$data</text>";
				})
				->addColumn('actions', function($row){
					$txt = "
                      <button class='btn btn-sm btn-primary' title='Edit' onclick='formAdd(`$row->id_layanan`)'><i class='fadeIn animated bx bxs-file' aria-hidden='true'></i></button>
                      <button class='btn btn-sm btn-danger' title='Delete' onclick='hapus(`$row->id_layanan`)'><i class='fadeIn animated bx bxs-trash' aria-hidden='true'></i></button>
					";
					return $txt;
				})
				->rawColumns(['actions','modifyNama','formatHarga','modifyDesc','modifyKategori','modifyJenis'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.mcu.layanan.main', $data);
    }

    public function form(Request $request) {
        if (empty($request->id)) {
            $data['layanan'] = '';
            $data['title'] = "Tambah ".$this->title;
		}else{
			$data['layanan'] = LayananMcu::where('id_layanan', $request->id)->first();
            $data['title'] = "Edit ".$this->title;
		}
        $content = view('admin.mcu.layanan.form', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    public function store(Request $request)
    {
        $rules = array(
            'jenis_layanan' => 'required',
            'deskripsi' => 'required',
            'harga' => 'required',
            'nama_layanan' => 'required',
            'kategori_layanan' => 'required',
            'total_kuota_layanan' => 'required'
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
                    $data = new LayananMcu;
                } else {
                    $data = LayananMcu::where('id_layanan', $request->id)->first();
                }
                $data->kategori_layanan = $request->kategori_layanan;
                $data->nama_layanan     = $request->nama_layanan;
                $data->harga            = preg_replace("/[^0-9]/", "", $request->harga);
                $data->deskripsi        = $request->deskripsi;
                $data->jenis_layanan    = $request->jenis_layanan;
                $data->maksimal_peserta = ($request->maksimal_peserta)?$request->maksimal_peserta:null;
                $data->kuota_layanan    = $request->total_kuota_layanan;
                // $data->max_jarak        = ($request->total_max_jarak)?$request->total_max_jarak:null;
                // $data->biaya_per_km     = ($request->biaya_per_km)?preg_replace("/[^0-9]/", "", $request->biaya_per_km):null;
                $data->save();

                if ($data) {
                    return ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
                }
                return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
            } catch (\Throwable $e) {
                $log = ['ERROR SIMPAN LAYANAN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = LayananMcu::where('id_layanan', $request->id)->first();
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
