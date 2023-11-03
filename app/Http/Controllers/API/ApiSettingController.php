<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersAndroid;
use App\Helpers\Helpers as Help;
use Validator, DB;

class ApiSettingController extends Controller
{
    public function getDataLogin($id) {
        try {
			$data = User::find($id);
			if ($data) {
				return Help::custom_response(200, "success", "OK", $data);
			} else {
                return Help::custom_response(500, "error", "Data not found", null);
            }
		} catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET DATA LOGIN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
			return Help::resApi(500, "error", $e->getMessage(), null);
		}
    }
    public function updateDataLogin(Request $request) {
        $validate = Validator::make($request->all(),[
            'id' => 'required',
            'username' => 'required',
            'password' => 'required'
        ],[
            'id.required'       => 'Id user tidak ditemukan',
            'username.required' => 'Username Wajib Diisi',
            'password.required' => 'Password Wajib Di isi'
        ]);
        if (!$validate->fails()) {
            try {
                $data = User::where('id',$request->id)->first(); #Save to users
                $data->username         = $request->username;
                $data->password         = bcrypt($request->password);
                $data->lihat_password   = $request->password;
                $data->save();
                if (!$data) {
                    return Help::resApi('Registrasi gagal.',500);
                }
                return Help::custom_response(200, "success", "OK", $data);
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR UPDATE DATA LOGIN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::resApi($validate->errors()->all()[0],500);
        }
    }
    public function getProfileUser($id) {
        try {
			$data = User::leftJoin('users_android as ua','ua.user_id','users.id')->where('users.id',$id)->first();
			if ($data) {
				return Help::custom_response(200, "success", "OK", $data);
			} else {
                return Help::custom_response(500, "error", "Data not found", null);
            }
		} catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET PROFILE USER ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
			return Help::resApi(500, "error", $e->getMessage(), null);
		}
    }
    public function updateProfileUser(Request $request) {
        $validate = Validator::make($request->all(),[
            'id' => 'required',
            'nik' => 'required',
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'telepon' => 'required',
        ],[
            'id.required'       => 'Id user tidak ditemukan',
            'nik.required' => 'NIK Wajib Diisi',
            'nama.required' => 'Nama Wajib Di isi',
            'tempat_lahir.required' => 'Tempat Lahir Wajib Diisi',
            'tanggal_lahir.required' => 'Tanggal Lahir Wajib Diisi',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Diisi',
            'alamat.required' => 'Alamat Wajib Diisi',
            'telepon.required' => 'Telepon Wajib Diisi',
        ]);
        if (!$validate->fails()) {
            try {
                if (strlen($request->nik)!=16) {
                    return Help::resAjax(['message'=>'NIK tidak sesuai standar 16 digit','code'=>500]);
                }
                DB::beginTransaction();
                $data = User::where('id',$request->id)->first(); #Save to users
                $data->name    = strtoupper($request->nama);
                $data->telepon = $request->telepon;
                $data->save();
                if (!$data) {
                    DB::rollback();
                    return Help::resApi('Update profile user gagal.',500);
                }
                $data2 = UsersAndroid::where('user_id',$request->id)->first();
                $data2->nik             = $request->nik;
                $data2->tempat_lahir    = $request->tempat_lahir;
                $data2->tanggal_lahir   = date('Y-m-d', strtotime($request->tanggal_lahir));
                $data2->jenis_kelamin   = $request->jenis_kelamin;
                $data2->alamat          = $request->alamat;
                $data2->save();
                if (!$data) {
                    DB::rollback();
                    return Help::resApi('Update profile user gagal.',500);
                }
                DB::commit();
                return Help::custom_response(200, "success", "OK", $data);
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR UPDATE PROFILE USER ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::resApi($validate->errors()->all()[0],500);
        }
    }
}
