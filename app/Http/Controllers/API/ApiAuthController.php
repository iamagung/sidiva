<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Authentication;
use App\Models\UsersAndroid;
use Twilio\Rest\Client;
use App\Helpers\Helpers as Help;
use Validator, DB, Auth, Hash, Log;

class ApiAuthController extends Controller
{
    private static $file = 'ApiAuthController.php';

    public function register(Request $request) {
        $validate = Validator::make($request->all(),[
            'nik' => 'required',
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'telepon' => 'required',
        ],[
            'nik.required' => 'No KTP (NIK) Wajib Diisi',
            'nama.required' => 'Nama Lengkap Wajib Di isi',
            'tempat_lahir.required' => 'Tempat Lahir Wajib Di isi',
            'tanggal_lahir.required' => 'Tanggal Lahir Wajib Di isi',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Di isi',
            'alamat.required' => 'Alamat Wajib Diisi',
            'telepon.required' => 'Telepon Wajib Diisi'
        ]);
        if (!$validate->fails()) {
            if (strlen($request->nik)!=16) { #Pengecekan NIK 16 digit
                return Help::resApi('NIK tidak sesuai standar 16 digit.',400);
            }
            if (Help::checkRegistPasien('nik',$request->nik)) { #Pengecekan berdasarkan NIK
                return Help::resApi('NIK Sudah Pernah Didaftarkan.',400);
            }
            if (Help::checkRegistPasien('telepon',$request->telepon)) { #Pengecekan berdasarkan No.Telepon
                return Help::resApi('No.Telepon Sudah Pernah Didaftarkan.',400);
            }
            try {
                DB::beginTransaction();
                $check_nik = $this->checkNIK($request->nik);
                if ($check_nik > 0) {
                    return Help::resApi('NIK sudah terdaftar.',400);
                }
                $data = new User; #Save to users
                $data->name             = strtoupper($request->nama);
                $data->username         = $request->nik;
                $data->level            = 'pasien';
                $data->password         = bcrypt($request->nik);
                $data->lihat_password   = $request->nik;
                $data->telepon          = $request->telepon;
                $data->save();
                if (!$data) {
                    DB::rollback();
                    return Help::resApi('Registrasi gagal.',400);
                }
                $data2 = new UsersAndroid; #Save to users android
                $data2->user_id         = $data->id;
                $data2->nik             = $request->nik;
                $data2->tempat_lahir    = $request->tempat_lahir;
                $data2->tanggal_lahir   = date('Y-m-d', strtotime($request->tanggal_lahir));
                $data2->jenis_kelamin   = $request->jenis_kelamin;
                $data2->alamat          = $request->alamat;
                $data2->save();
                if (!$data2) {
                    DB::rollback();
                    return Help::resApi('Gagal menyimpan ke users android.',400);
                }
                $token = $data->createToken('auth_token')->plainTextToken;
                DB::commit();
                return response()->json([
                    'metadata' => [
                        'message'       => 'Berhasil',
                        'code'          => 200,
                        'access_token'  => $token,
                        'token_type'    => 'Bearer'
                    ],
                    'response' => $data,
                ]);
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR REGISTER PASIEN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::custom_logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::resApi($validate->errors()->all()[0],400);
        }
    }
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('username', 'password'))) {
            return Help::resApi('Unauthorized.',401);
        }
        try {
            $user = User::leftJoin('users_android as ua','ua.user_id','users.id')
                ->where('users.username', $request->username)
                ->whereIn('users.level',['pasien','dokter','perawat'])
                ->first();
            if (!$user) {
                return Help::resApi('User not found.',400);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'metadata' => [
                    'message'       => 'User found',
                    'code'          => 200,
                    'access_token'  => $token,
                    'token_type'    => 'Bearer'
                ],
                'response' => $user,
            ]);
        } catch (\Throwable $e) {
            $log = ['ERROR REGISTER PASIEN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::custom_logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return Help::resApi('You have successfully logged out and the token was successfully deleted',200);
    }
    public function sendOtp(Request $request) {
        $arrAdmin = [ # For send error notif to WhatsApp Admin
			'title' => 'ERROR SEND OTP',
			'url'   => $request->url(),
			'file'  => self::$file,
			'data'  => $request->all(),
		];
        try {
            DB::beginTransaction();
            $request->otp = rand(10000, 99999);
            $request->tgljam = date('Y-m-d H:i:s');
            $insert_auth = Authentication::insert($request);
            if(!$insert_auth){
                return Help::resApi('Gagal menyimpan authentication.',400);
			}
            $request->request->add([ # For Helpers::messageSenderOtp()
                'otp' => $insert_auth->otp,
                'phone' => $insert_auth->wa
            ]);
            $sendOtp = Help::messageSenderOtp($request);
            // if ($sendOtp->status==false) { # Jika gagal send otp
            //     DB::rollback();
            //     $arrAdmin['title'] = 'WHATSAPP OTP FAILED';
            //     $arrAdmin['data'] = ['phone'=>$insert_auth->wa,'otp'=>$insert_auth->otp];
            //     $arrAdmin['message'] = "Kode OTP gagal dikirim ke WA pasien "."$insert_auth->wa";
            //     Help::custom_logging($arrAdmin);
            //     return Help::resApi('Send OTP gagal, silahkan coba lagi',500);
            // }
            DB::commit();
            return Help::resApi('Send OTP Berhasil berhasil',200);
        } catch (\Throwable $e) {
            $log = ['ERROR SEND OTP ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',400);
        }
    }
    public function verifyOtp(Request $request){
        try {
			$cek = Authentication::check_otp($request);
			if ($cek) {
				if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($cek->tanggal_waktu.'+ 2 hours')))
                    return Help::custom_response(400, "error", "OTP Expired!", null);

                Authentication::update_expired($request);
				$user = User::where('telepon', '=', $cek->wa)->first();
				return $this->set_token($user);
			}

			return Help::custom_response(404, "error", "Not Found / Expired", null);
		} catch (\Throwable $e) {
			return Help::custom_response(500, "error", $e->getMessage(), null);
		}
    }
    public function set_token($user){
		$token = $user->createToken('auth_token')->plainTextToken;
		$data = [
			'message' => 'success',
			'bearer_token' => $token,
			'user' => $user,
		];

		return Help::custom_response(200, "success", "OK", $data);
	}
    public function checkNIK($nik) { //check nik apakah sudah digunakan
        $check = User::where('username','=',$nik)->count();
        return $check;
    }
}
