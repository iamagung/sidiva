<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Authentication;
use App\Models\TmCustomer;
use Twilio\Rest\Client;
use App\Helpers\Helpers as Help;
use Validator, DB, Auth, Hash;

class ApiAuthController extends Controller
{
    private static $file = 'ApiAuthController.php';

    public function registerPasien(Request $request) {
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
                return Help::resApi('NIK tidak sesuai standar 16 digit.',201);
            }
            if (Help::checkRegistPasien('nik',$request->nik)) { #Pengecekan berdasarkan NIK
                return Help::resApi('NIK Sudah Pernah Didaftarkan.',201);
            }
            if (Help::checkRegistPasien('telepon',$request->telepon)) { #Pengecekan berdasarkan No.Telepon
                return Help::resApi('No.Telepon Sudah Pernah Didaftarkan.',201);
            }
            try {
                return 'ok';
                DB::beginTransaction();
                $data = new User; #Save to users
                $data->name             = strtoupper($request->nama);
                $data->username         = $request->nik;
                $data->level            = 'pasien';
                $data->password         = $request->nik;
                $data->lihat_password   = $request->nik;
                $data->telepon          = $request->telepon;
                $data->save();

                if ($data) {
                    # save to dbsimars=>tmcustomer
                    $checkCustomer = TmCustomer::where('NoKtp',$request->nik)->first();
                    if ($checkCustomer) {
                        $customer = $checkCustomer;
                    } else {
                        $customer = new TmCustomer;
                        $customer->KodeCust = Help::generateRM();
                        $customer->NoKtp    = $request->nik;
                    }
                    $customer->NamaCust = strtoupper($request->nama);
                    $customer->Tempat   = $request->tempat_lahir;
                    $customer->TglLahir = date('Y-m-d', strtotime($request->tanggal_lahir));
                    $customer->JenisKel = $request->jenis_kelamin;
                    $customer->Alamat   = $request->alamat;
                    $customer->Telp     = $request->telepon;
                    $customer->save();
                    if (!$customer) {
                        return Help::resApi('Gagal Menyimpan Data Customer.',201);
                        DB::rollback();
                    }
                    DB::commit();
                    return response()->json([
                        'metadata' => [
                            'message' => 'Berhasil',
                            'code'    => 200,
                        ],
                        'response' => User::where('username',$request->nik)->first(),
                    ]);
                }else{
                    return response()->json([
                        'metadata' => [
                            'message' => 'Error',
                            'code'    => 201,
                        ],
                        'response' => [],
                    ]);
                }
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR REGISTER PASIEN ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 500,
                ],
                'response' => [],
            ]);
        }
    }
    public function sendOtp(Request $request) {
        $arrAdmin = [ # For send error notif to WhatsApp Admin
			'title' => 'LINK CHECK-IN DATA NOT FOUND',
			'url'   => $request->url(),
			'file'  => self::$file,
			'data'  => $request->all(),
		];
        try {
            DB::beginTransaction();
            $request->otp = rand(10000, 99999);
            $request->tgljam = date('Y-m-d H:i:s');
            $insert_auth = Authentication::insert($request);
            if($insert_auth){
                // $callback = ['otp'=>$insert_auth->otp,'notelp'=>$insert_auth->wa];
                $request->request->add([ # For Helpers::messageSenderAntrian()
                    'message' => Help::textNomorAntrianPasien($insert_auth->otp),
                    'phone' => $insert_auth->wa
                ]);
                $sendOtp = Helpers::messageSenderOtp($request);
                foreach($sendOtp->data->messages as $key => $val){
                    if(!$val->status){
                        DB::rollback();
                        $arrAdmin['title'] = 'WHATSAPP OTP FAILED';
                        $arrAdmin['data'] = ['phone'=>$insert_auth->wa,'otp'=>$insert_auth->otp];
                        $arrAdmin['message'] = "Kode OTP gagal dikirim ke WA pasien wa.me/$insert_auth->wa";
                        Helpers::sendErrorSystemToAdmin($arrAdmin); # Send notif error to wa admin
                        $request->request->add(['toAdmin' => true]); # For Helpers::messageSenderAntrian()
                        Helpers::messageSenderAntrian($request); # Nomor antrian yang gagal dikirim ke pasien, akan di kirim ke admin
                        Helpers::logging($arrAdmin);
                        return Helpers::resApi('Send OTP gagal, silahkan coba lagi',500);
                    }
                }
                DB::commit();
                return Helpers::resApi('Send OTP Berhasil berhasil',200);
			}
        } catch (\Throwable $e) {
            $log = ['ERROR SEND OTP ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
