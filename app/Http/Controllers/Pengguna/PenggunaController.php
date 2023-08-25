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
		$this->title = 'Data User';
	}

    public function main(Request $request)
    {
        try {
            if(request()->ajax()){
                $data = Pengguna::where('poli_id', '!=', '')->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('poli', function($row){
                        $poli = DB::connection('dbrsud')->table('tm_poli')->where('KodePoli', $row->poli_id)->first()->NamaPoli;
                        return $poli;
                    })
                    ->addColumn('actions', function($row){
                        $txt = "
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
            $data['title'] = "Tambah ".$this->title;
		}else{
			$data['data'] = Pengguna::where('id', $request->id)->first();
            $data['title'] = "Edit ".$this->title;
		}
        $data['dokter'] = DB::connection('dbwahidin')->table('tm_setupall')->where('groups','Dokter')
            ->where('nilaichar', '!=', '')->get();
        $data['poli'] = DB::connection('dbrsud')->table('tm_poli')
            ->rightJoin('mapping_poli_bridging as mpb', 'tm_poli.KodePoli', 'mpb.kdpoli_rs')->get();
        $content = view('admin.pengguna.form', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    public function store(Request $request)
    {
        // return $request->all();
        if (!empty($request->id)) {
            $rules = array(
                'username'  => 'required',
                'poli'      => 'required',
                'name_user' => 'required',
            );
        } else {
            $rules = array(
                'username'  => 'required',
                'password'  => 'required',
                'poli'      => 'required',
                'name_user' => 'required',
            );
        }
        $messages = array(
            'required'  => 'Kolom Harus Diisi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        } else {
            try {
                $lvl = Pengguna::orderBy('id', 'DESC')->first()->level;
                if (!empty($request->id)) {
                    $user = Pengguna::where('id', $request->id)->first();
                    if (!empty($request->password)) {
                        $user->password = bcrypt($request->password);
                    }
                } else {
                    $user = new Pengguna;
                    $user->password = bcrypt($request->password);
                }
                $tmSetupall = DB::connection('dbwahidin')->table('tm_setupall')->where('setupall_id', $request->name_user)->first();
                $user->name_user = $tmSetupall->nilaichar;
                $user->kode_dokter = $request->name_user;
                $user->poli_id = $request->poli;
                $user->email = $request->username;
                $user->level = $lvl+1;
                $user->lv_user = $request->username;
                $user->phone = $request->telp;
                $user->address_user = $request->alamat;
                $user->active = $request->is_active;
                if (!empty($request->photo_user)) {
                    date_default_timezone_set('Asia/Jakarta');
                    $ukuranFile = filesize($request->photo_user);
                    if ($ukuranFile <= 500000) {
                        $ext_foto = $request->photo_user->getClientOriginalExtension();
                        $filename = "User_".date('Ymd-His')."_".$request->alias.".".$ext_foto;
                        $temp_foto = 'uploads/users/';
                        $proses = $request->photo_user->move($temp_foto, $filename);
                        $user->photo_user = $filename;
                    }else{
                        $file=$_FILES['photo_user']['name'];
                        if(!empty($file)){
                            $direktori="uploads/users/"; //tempat upload foto
                            $name='photo_user'; //name pada input type file
                            $namaBaru="User_".date('Ymd-His')."_".$request->alias; //name pada input type file
                            $quality=50; //konversi kualitas gambar dalam satuan %
                            $upload = compressFile::UploadCompress($namaBaru,$name,$direktori,$quality);
                        }
                        $ext_foto = $request->photo_user->getClientOriginalExtension();
                        $user->photo_user = $namaBaru.".".$ext_foto;
                    }
                }
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
