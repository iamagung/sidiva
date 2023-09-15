<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengguna;
use App\Http\Libraries\compressFile;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth, Hash;

class PenggunaController extends Controller
{
    function __construct()
	{
		$this->title = 'Data Pengguna';
	}

    public function main(Request $request)
    {
        try {
            if(request()->ajax()){
                $data = Pengguna::orderBy('id','ASC')->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('lvl', function($row){
                        if ($row->level=='1') {
                            $txt = 'Admin Utama';
                        } else if ($row->level=='2') {
                            $txt = 'Admin MCU';
                        } else if ($row->level=='3') {
                            $txt = 'Admin Homecare';
                        } else {
                            $txt = 'Admin Telemedicine';
                        }
                        return $txt;
                    })
                    ->addColumn('actions', function($row){
                        $txt = "
                        <button class='btn btn-sm btn-secondary' title='Reset' onclick='reset(`$row->id`)'><i class='fadeIn animated bx bx-refresh' aria-hidden='true'></i></button>
                        <button class='btn btn-sm btn-primary' title='Edit' onclick='formAdd(`$row->id`)'><i class='fadeIn animated bx bxs-file' aria-hidden='true'></i></button>
                        <button class='btn btn-sm btn-danger' title='Delete' onclick='hapus(`$row->id`)'><i class='fadeIn animated bx bxs-trash' aria-hidden='true'></i></button>
                        ";
                        return $txt;
                    })
                    ->rawColumns(['actions'])
                    ->toJson();
            }
            $data['title'] = $this->title;
            return view('admin.pengguna.main', $data);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET USER TM ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function form(Request $request)
    {
        if (empty($request->id)) {
            $data['data'] = '';    
		}else{
            $data['data'] = Pengguna::where('id',$request->id)->first();
		}
        $content = view('admin.pengguna.modal', $data)->render();
		return ['content'=>$content];
    }

    public function store(Request $request)
    {
        try {
            if (!empty($request->id)) {
                $user = Pengguna::where('id', $request->id)->first();
                if (!empty($request->password)) {
                    $user->password = bcrypt($request->password);
                }
            } else {
                $user = new Pengguna;
                $user->password = bcrypt($request->password);
            }
            $user->name     = $request->nama;
            $user->level    = $request->level;
            $user->telepon  = $request->telepon;
            $user->username = $request->username;
            $user->email    = $request->username."@gmail.com";
            $user->save();

            if ($user) {
                return ['code' => 200, 'status' => 'success', 'message' => 'User Berhasil Ditambahkan.'];
            }else{
                return ['code' => 201, 'status' => 'error', 'message' => 'User Gagal Ditambahkan.'];
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
            $cari = DB::connection('dbapm')->table('users')->where('id', $request->id)->first();
            if($cari->photo_user != ''){
                if(file_exists('uploads/users/'.$cari->photo_user)){
                    unlink('uploads/users/'.$cari->photo_user);
                }
            }
            $delete = DB::connection('dbapm')->table('users')->where('id', $request->id)->delete();
    
            if ($delete) {
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
