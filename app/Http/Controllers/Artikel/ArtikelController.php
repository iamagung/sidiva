<?php

namespace App\Http\Controllers\Artikel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helpers as Help;
use App\Models\Artikel;
use DataTables, DB;

class ArtikelController extends Controller
{
    function __construct(){
		$this->title = 'Artikel Kesehatan';
	}

    public function main(Request $request) {
        if(request()->ajax()){
            $data = Artikel::orderBy('id_artikel_kesehatan','DESC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					$txt = "
                    <button class='btn btn-secondary' title='Edit' onclick='formAdd(`$row->id_artikel_kesehatan`)'><i class='fadeIn animated bx bxs-edit' aria-hidden='true'></i></button>
                    <button class='btn btn-danger' title='Delete' onclick='hapus(`$row->id_artikel_kesehatan`)'><i class='fadeIn animated bx bxs-trash' aria-hidden='true'></i></button>
					";
					return $txt;
				})
                ->addColumn('modifyTanggal', function($row){
					return substr($row->created_at,0,10);
				})
				->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.artikel.main', $data);
    }

    public function form(Request $request) {
        if (empty($request->id)) {
            $data['data'] = '';
            $data['title'] = "Tambah ".$this->title;
		}else{
			$data['data'] = Artikel::where('id_artikel_kesehatan', $request->id)->first();
            $data['title'] = "Edit ".$this->title;
		}
        $content = view('admin.artikel.form', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    public function store(Request $request) {
        try {
            if (empty($request->id)) {
                $data = new Artikel;
                $data->judul    = $request->judul;
                $data->isi      = $request->deskripsi;
                $data->save();
            } else {
                $param = [
                    'judul' => $request->judul,
                    'isi' => $request->deskripsi
                ];
                $data = Artikel::where('id_artikel_kesehatan',$request->id)->update($param);
            }
            if (!$data) {
                return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
            }
            return ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
        } catch (\Throwable $e) {
            $log = ['ERROR SIMPAN ARTIKEL KESEHATAN('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function delete(Request $request) {
        try {
            $data = Artikel::where('id_artikel_kesehatan', $request->id)->delete();
            if (!$data) {
               return ['type' => 'success', 'status' => 'success', 'code' => '201'];
            }
            return ['type' => 'success', 'status' => 'success', 'code' => '200'];
        } catch (\Throwable $e) {
            $log = ['ERROR DELETE ARTIKEL KESEHATAN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
