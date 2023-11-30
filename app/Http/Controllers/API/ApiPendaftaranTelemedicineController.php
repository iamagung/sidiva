<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PermintaanTelemedicine;
use App\Models\PengaturanTelemedicine;
use App\Models\TenagaMedisTelemedicine;
use App\Models\PaymentPermintaan;
use App\Models\Rating;
use App\Models\ResepObat;
use App\Models\ResepObatDetail;
use App\Models\JadwalTenagaMedis;
use App\Models\TmCustomer;
use App\Models\Activity;
use App\Models\User;
use App\Models\UserAndroid;
use App\Models\VideoConference;
use App\Models\DBRSUD\TmPoli;
use Illuminate\Http\Request;
use App\Helpers\Helpers as Help;
use App\Helpers\XenditHelpers;
use Illuminate\Support\Facades\Auth;
use Validator, DB, DateTime;

class ApiPendaftaranTelemedicineController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Jakarta');
    }

    public function pesanJadwalTelemedicine(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'nik' => 'required',
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required',
            'keterangan' => 'required',
            'poli_id' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'keluhan' => 'required',
            'biaya_layanan' => 'required',
            'no_telepon' => 'required',
            'jadwal_dokter' => 'required|regex:/\d{2}:\d{2}-\d{2}:\d{2}/',
            'tenaga_medis_id' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'jarak' => 'required',
        ],[
            'nik.required' => 'Nik Wajib Di isi',
            'nama.required' => 'Nama Wajib Di isi',
            'tempat_lahir.required' => 'Tempat Lahir Wajib Di isi',
            'tanggal_lahir.required' => 'Tanggal Lahir Wajib Di isi',
            'tanggal_lahir.date' => 'Tanggal Lahir harus berformat yyyy-mm-dd',
            'alamat.required' => 'Alamat Wajib Di isi',
            'keterangan.required' => 'Keterangan Wajib Di isi',
            'poli_id.required' => 'Poli Wajib Di isi',
            'keluhan.required' => 'Keluhan  Wajib Di isi',
            'biaya_layanan.required' => 'Biaya Layanan  Wajib Di isi',
            'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Di isi',
            'tanggal_kunjungan.date' => 'Tanggal Kunjungan harus berformat yyyy-mm-dd',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Di isi',
            'jenis_kelamin.in' => 'Jenis Kelamin yang di inputkan salah',
            'no_telepon.required' => 'No. telepon Wajib Di isi',
            'jadwal_dokter.required' => 'Jadwal Dokter Wajib Di isi',
            'tenaga_medis_id.required' => 'Tenaga Medis Wajib Di isi',
            'jadwal_dokter.regex' => 'Jadwal Dokter Harus Menggunakan Format H:i-H:i, cth:(08:00-09:00)',
            'longitude.required' => 'Lokasi Pasien (Longitude) Wajib Di isi',
            'latitude.required' => 'Lokasi Pasien (Latitude) Wajib Di isi',
            'jarak.required' => 'Jarak Wajib Di isi',
        ]);
        if (!$validate->fails()) {
            // return $request;
            $tanggal = strtotime($request->tanggal_kunjungan);
            $request->tanggal_kunjungan = date('Y-m-d', $tanggal);
			$timeCur = date('H:i');
			$dateCur = date('Y-m-d');
            $curDiff = date_create($dateCur);
            $kunjDiff = date_create($request->tanggal_kunjungan);
			$dayName = date('D',strtotime($request->tanggal_kunjungan));
            if(!$dokter = TenagaMedisTelemedicine::where('nakes_id', $request->tenaga_medis_id)->where('jenis_nakes', 'dokter')->first()) {
                return Help::resApi('Tenaga Kesehatan / Dokter yang dipilih tidak tersedia',400);
            }
            if(!$pengaturan = PengaturanTelemedicine::where('id_pengaturan_telemedicine', 1)->first()) {
                return Help::resApi('Tidak ada jadwal yang di setting',400);
            }
            if ($dayName == 'Mon') { # Senin
                if ($pengaturan->seninBuka == '' || $pengaturan->seninTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari Senin.',400);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->seninBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->seninBuka,400);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->seninTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->seninTutup,400);
                    }
                }
            }
            if ($dayName == 'Tue') { # Selasa
                if ($pengaturan->selasaBuka == '' || $pengaturan->selasaTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari Selasa.',400);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->selasaBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->selasaBuka,400);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->selasaTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->selasaTutup,400);
                    }
                }
            }
            if ($dayName == 'Wed') { # Rabu
                if ($pengaturan->rabuBuka == '' || $pengaturan->rabuTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari Rabu.',400);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->rabuBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->rabuBuka,400);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->rabuTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->rabuTutup,400);
                    }
                }
            }
            if ($dayName == 'Thu') { # Kamis
                if ($pengaturan->kamisBuka == '' || $pengaturan->kamisTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari Kamis.',400);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->kamisBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->kamisBuka,400);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->kamisTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->kamisTutup,400);
                    }
                }
            }
            if ($dayName == 'Fri') { # Jum'at
                if ($pengaturan->jumatBuka == '' || $pengaturan->jumatTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari Jumat.',400);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->jumatBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->jumatBuka,400);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->jumatTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->jumatTutup,400);
                    }
                }
            }
            if ($dayName == 'Sat') { # Sabtu
                if ($pengaturan->sabtuBuka == '' || $pengaturan->sabtuTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari Sabtu.',400);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->sabtuBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->sabtuBuka,400);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->sabtuTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->sabtuTutup,400);
                    }
                }
            }
			if($dayName == 'Sun'){ # If tanggal periksa adalah hari minggu
				return Help::resApi('Tidak bisa mengambil antrian pada hari minggu.',400);
			}
            if($request->tanggal_kunjungan<$dateCur){ # If tanggal periksa kemarin{back date}
                return Help::resApi('Tanggal sudah terlewat.',400);
            }else if(date_diff($curDiff,$kunjDiff)->format('%a')<1) {
                return Help::resApi('Tanggal Kunjungan Ditolak, Tanggal kunjungan Minimal H-1 dari tanggal permintaan.',400);
            }else if(date_diff($curDiff,$kunjDiff)->format('%a')>3) {
                return Help::resApi('Tanggal Kunjungan Ditolak, Tanggal kunjungan Maksimal H-3 dari tanggal permintaan.',400);
            }
            $pasien = TmCustomer::where('NoKtp','=',$request->nik)->first();
            if (empty($pasien)) {
                $pasien = TmCustomer::where('KodeCust','=',$request->no_rm)->first();
            }
            if (strlen($request->nik)!=16) {
                return Help::resApi('NIK tidak sesuai standar 16 digit.',400);
            }
            // if($request->jenis_pembayaran == 'BPJS') {
            //     if (strlen($request->no_bpjs)!=13) {
            //         return Help::resApi('Nomor BPJS tidak sesuai standar 13 digit.',400);
            //     }
            // }
            try {
                DB::beginTransaction();
                $noreg = Help::generateNoRegTelemedicine($request);
                // $strRandom = Help::randomString(7);
                $data = new PermintaanTelemedicine;
                $data->no_rm                = !empty($pasien) ? $pasien->KodeCust : null;
                $data->no_registrasi        = $noreg;
                // $data->kode_booking         = $strRandom;
                $data->nik                  = !empty($pasien) ? $pasien->NoKtp : $request->nik;
                $data->nama                 = !empty($pasien) ? $pasien->NamaCust : strtoupper($request->nama);
                $data->alamat               = !empty($pasien) ? $pasien->Alamat : $request->alamat;
                $data->tanggal_order        = date('Y-m-d H:i:s');
                $data->tanggal_kunjungan    = $request->tanggal_kunjungan;
                $data->jenis_pembayaran     = $request->jenis_pembayaran ? $request->jenis_pembayaran : null;
                // $data->no_bpjs              = !empty($request->no_bpjs) ? $request->no_bpjs : null;
                // $data->no_rujukan           = !empty($request->no_rujukan) ? $request->no_rujukan : null;
                $data->tanggal_lahir        = (!empty($pasien) && ($pasien->TglLahir != null)) ? $pasien->TglLahir : $request->tanggal_lahir;
                $data->jenis_kelamin        = !empty($pasien) ? $pasien->JenisKel : $request->jenis_kelamin;
                $data->no_telepon           = !empty($pasien) ? (($pasien->Telp == '' || $pasien->Telp == null) ? $request->no_telepon : $pasien->Telp) : $request->no_telepon;
                $data->tenaga_medis_id      = $request->tenaga_medis_id;
                $data->jadwal_dokter        = $request->jadwal_dokter;
                $data->poli_id              = $request->poli_id;
                $data->tempat_lahir         = $request->tempat_lahir;
                $data->biaya_layanan        = $request->biaya_layanan;
                $data->longitude            = $request->longitude;
                $data->latitude             = $request->latitude;
                $data->keterangan           = $request->keterangan;
                $data->keluhan              = $request->keluhan;
                $data->jarak                = $request->jarak;
                $data->metode_pembayaran    = $request->metode_pembayaran ? $request->metode_pembayaran : null;
                $data->status_pasien        = 'belum';
                $data->status_pembayaran    = 'belum';
                // return $data;
                if(!$data->save()){
                    DB::rollback();
                    return Help::resApi('Gagal saat menyimpan data permintaan telemedicine',400);
                }

                $payment = new PaymentPermintaan;
                $payment->permintaan_id = $data->id_permintaan_telemedicine;
                $payment->nominal = $data->biaya_layanan;
                $payment->jenis_layanan = 'telemedicine';
                $payment->tgl_expired = date('Y-m-d H:i:s', strtotime('+30 minute'));
                $payment->status = 'UNCONFIRMED';
                if(!$payment->save()){
                    DB::rollback();
                    return Help::resApi('Gagal saat menyimpan data, data pembayaran tidak valid',400);
                }

                if(!$activity = Activity::store(Auth::user()->id,'Booking telemedicine')) {
                    DB::rollback();
                    return Help::resApi('Gagal booking telemedicine',400);
                }

                if ($data) {
                    DB::commit();
                    return Help::resApi('Berhasil menambah permintaan',200,Help::callbackRegistTelemedicine($data));
                }else{
                    DB::rollback();
                    return Help::resApi('Gagal melakukan permintaan',400);
                }
            } catch (\Throwable $e) {
                DB::rollback();
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR PENDAFTARAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::resApi($validate->errors()->all()[0],400);
        }
    }

    public function getFormPermintaan() {
        try {
            if(!$user = User::select('id','name','telepon')->where('id',Auth::user()->id)->has('users_android')->with('users_android:user_id,nik,tempat_lahir,tanggal_lahir,jenis_kelamin,alamat')->first()) {
                return Help::resApi('Gagal mendapatkan data user, data tidak ditemukan',204);
            }
            return Help::resApi('Data user berhasil didapatkan',200,$user);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET FORM PERMINTAAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getBiayaTelemedicine() {
        try {
            if(!$pengaturan = PengaturanTelemedicine::first()){
                return Help::resApi('Layanan sementara tidak dapat digunakan, pengaturan biaya layanan tidak ditemukan', 400);
            }
            return Help::resApi('Berhasil', 200, $pengaturan->tarif);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET BIAYA TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getListTelemedicine(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id_user' => 'required'
        ],[
            'id_user.required' => 'ID User Wajib Di isi'
        ]);
        if (!$validate->fails()) {
            $sekarang = date('Y-m-d');
            try{
                $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order as tanggal_transaksi', 'tenaga_medis_id', 'poli_id', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'biaya_layanan as total_biaya')
                    // ->has('tmPoli')
                    ->with('tmPoli:KodePoli,NamaPoli as nama_poli')
                    ->has('dokter')
                    ->with('dokter', function($q) {
                        $q->select('nakes_id')->has('user_ranap')->with('user_ranap:id,name as nama_dokter');
                    })
                    ->whereHas('user_android',function ($q) use($request) {
                        $q->where('user_id', '=', $request->id_user);
                    })
                    ->with('payment_permintaan')
                    ->whereIn('status_pasien', ['menunggu', 'belum', 'proses'])
                    ->where('status_pembayaran', 'belum')
                    ->where('tanggal_kunjungan', '>', $sekarang)
                    ->get();
                // $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order as tanggal_transaksi', 'tm_poli.NamaPoli as nama_poli', 'nakes_user.name as nama_nakes', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'biaya_layanan as total_biaya')
                //     // ->join(DB::connection('dbrsud')->raw('dbsimars_baru.tm_poli as tm_poli'),'permintaan_telemedicine.poli_id','=','tm_poli.KodePoli')
                //     // ->join('tenaga_medis_telemedicine as nakes', 'permintaan_telemedicine.tenaga_medis_id', '=', 'nakes.nakes_id')
                //     ->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'nakes.nakes_id','=','nakes_user.id')
                //     ->join('users_android as ua', 'permintaan_telemedicine.nik', '=', 'ua.nik')
                //     ->where('ua.user_id', '=', $request->id_user)
                //     ->get();
                if (count($permintaan)>0) {
                    return Help::resApi('Berhasil',200,$permintaan);
                } else {
                    return Help::resApi('List Permintaan Tidak ditemukan',204);
                }

            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR GET LIST POLI TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::resApi($validate->errors()->all()[0],400);
        }
    }

    public function getListPelayananTelemedicine(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id_user' => 'required'
        ],[
            'id_user.required' => 'ID User Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }
        try{
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order as tanggal_transaksi', 'poli_id', 'tenaga_medis_id', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien')
            ->with('tmPoli:KodePoli,NamaPoli as nama_poli')
                ->has('dokter')
                ->with('dokter', function($q) {
                    $q->select('nakes_id')->has('user_ranap')->with('user_ranap:id,name as nama_dokter');
                })
                ->whereHas('user_android',function ($q) use($request) {
                    $q->where('user_id', '=', $request->id_user);
                })
                ->whereHas('video_conference')
                ->with('video_conference:permintaan_id,link_vicon')
                ->where('status_pasien', 'proses')
                ->where('status_pembayaran', 'lunas')
                ->whereDoesntHave('rekap_medik')
                ->whereDoesntHave('rekam_medis_lanjutan')
                ->orderBy('id_permintaan_telemedicine', 'DESC')
                ->get();
            if (count($permintaan) > 0) {
                return Help::resApi('Berhasil',200,$permintaan);
            } else {
                return Help::resApi('Tidak ada daftar pelayanan aktif',204);
            }

        } catch (\Throwable $e) {
            $log = ['ERROR GET LIST PELAYANAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function riwayatPermintaanTelemedicine(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id_user' => 'required'
        ],[
            'id_user.required' => 'ID User Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try{
            $permintaan1 = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order as tanggal_transaksi', 'poli_id', 'tenaga_medis_id', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'biaya_layanan as total_biaya')
                ->with('tmPoli:KodePoli,NamaPoli as nama_poli')
                ->has('dokter')
                ->with('rekap_medik')
                ->with('rekam_medis_lanjutan')
                ->with('resep_obat', function($q) {
                    $q->with('resep_obat_detail');
                })
                ->with('dokter', function($q) {
                    $q->select('nakes_id')->has('user_ranap')->with('user_ranap:id,name as nama_dokter');
                })
                ->whereHas('user_android',function ($q) use($request) {
                    $q->where('user_id', '=', $request->id_user);
                })
                ->whereIn('status_pasien', ['selesai','batal','tolak'])
                ->orderBy('id_permintaan_telemedicine', 'DESC');
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order as tanggal_transaksi', 'poli_id', 'tenaga_medis_id', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'biaya_layanan as total_biaya')
                ->with('tmPoli:KodePoli,NamaPoli as nama_poli')
                ->has('dokter')
                ->has('rekap_medik')
                ->has('rekam_medis_lanjutan')
                ->with('rekap_medik')
                ->with('rekam_medis_lanjutan')
                ->with('resep_obat', function($q) {
                    $q->with('resep_obat_detail');
                })
                ->with('dokter', function($q) {
                    $q->select('nakes_id')->has('user_ranap')->with('user_ranap:id,name as nama_dokter');
                })
                ->whereHas('user_android',function ($q) use($request) {
                    $q->where('user_id', '=', $request->id_user);
                })
                ->whereIn('status_pasien', ['proses'])
                ->orderBy('id_permintaan_telemedicine', 'DESC')
                ->union($permintaan1)
                ->get();
            // $permintaan = $permintaan1->union($permintaan2);
            // $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order as tanggal_transaksi', 'tm_poli.NamaPoli as nama_poli', 'nakes_user.name as nama_nakes', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'biaya_layanan as total_biaya')
            //     ->join(DB::connection('dbrsud')->raw('dbsimars_baru.tm_poli as tm_poli'),'permintaan_telemedicine.poli_id','=','tm_poli.KodePoli')
            //     ->join('tenaga_medis_telemedicine as nakes', 'permintaan_telemedicine.tenaga_medis_id', '=', 'nakes.nakes_id')
            //     ->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'nakes.nakes_id','=','nakes_user.id')
            //     ->join('users_android as ua', 'permintaan_telemedicine.nik', '=', 'ua.nik')
            //     ->where('no_rm', $params)
            //     ->where('status_pasien', 'belum')
            //     ->orderBy('id_permintaan_telemedicine', 'DESC')
            //     ->get();
            if (count($permintaan) > 0) {
                return Help::resApi('Berhasil',200,$permintaan);
            } else {
                return Help::resApi('Tidak ada riwayat',204);
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR RIWAYAT PERMINTAAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getPoli()
    {
        try{
            $poli = TmPoli::select('KodePoli','NamaPoli')
                ->join('mapping_poli_bridging as mpd', 'tm_poli.KodePoli', '=', 'mpd.kdpoli_rs')
                ->get();
            if (count($poli) > 0) {
                return Help::resApi('Berhasil',200,$poli);
            } else {
                return Help::resApi('Data Poli tidak ditemukan!',204);
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET LIST POLI TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getDokter(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'poli_id' => 'required',
            'tanggal_kunjungan' => 'required',
        ],[
            'poli_id.required' => 'Poli Wajib Di isi',
            'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Di isi'
        ]);
        if (!$validate->fails()) {
            try{
                $kunjungan = date('w', strtotime($request->tanggal_kunjungan));
                // $dokter = TenagaMedisTelemedicine::select('poli_id','nakes_id')
                //     ->whereHas('jadwalMedis', function ($q) use($kunjungan) {
                //         $q->where('jenis_pelayanan', 'telemedicine')
                //         ->where('hari', $kunjungan);
                //     })
                //     ->with('tmPoli:KodePoli,NamaPoli as nama_poli')
                //     ->with('user_ranap:id,name as nama_dokter')
                //     ->with(['permintaan' => function ($q) {
                //         $q->select('id_permintaan_telemedicine','tenaga_medis_id')->where('status_pasien','selesai')
                //         ->with('rating', function ($qq) {
                //             $qq->selectRaw('star_rating, permintaan_id');
                //         });
                //     }])
                //     ->where('tenaga_medis_telemedicine.jenis_nakes', 'dokter')
                //     ->where('poli_id', $request->poli_id)
                //     ->get();
                // return TmPoli::has('tenaga_medis_telemedicine')->with('tenaga_medis_telemedicine')->get();
                // return TenagaMedisTelemedicine::has('tm_poli')->with('tm_poli')->get();
                // return TenagaMedisTelemedicine::
                // // has('tm_poli')->
                // with('tm_poli')->
                // get();
                $base_url = url('/').'/storage/pengguna/';
                $default_image = url('/assets/images/no-image.jpg');
                $dokter = TenagaMedisTelemedicine::
                    select('tenaga_medis_telemedicine.poli_id','nakes_id',DB::raw('avg(rating.star_rating) as rate'))
                    ->whereHas('jadwalMedis', function ($q) use($kunjungan) {
                        $q->where('jenis_pelayanan', 'telemedicine')
                        ->where('hari', $kunjungan);
                    })
                    ->has('user')
                    ->with('user',function($q) use($base_url,$default_image) {
                        $q->selectRaw("(case when foto is null then '$default_image' else concat('$base_url', foto) end) foto,id");
                    })
                    ->whereExists(function($q){$q->select(DB::connection('dbrsud')->table('tm_poli')->where('tenaga_medis_telemedicine.poli_id','tm_poli.KodePoli')->raw(1));})
                    ->with('tmPoli:KodePoli,NamaPoli as nama_poli')
                    // ->whereExists(function($q){$q->select(DB::connection('dbrsud')->table('tm_poli')->where('tenaga_medis_telemedicine.poli_id','tm_poli.KodePoli')->raw(1));})
                    ->has('user_ranap')
                    ->with('user_ranap:id,name as nama_dokter')
                    ->rightJoin('permintaan_telemedicine','permintaan_telemedicine.tenaga_medis_id','=','tenaga_medis_telemedicine.nakes_id')
                    ->leftJoin('rating','rating.permintaan_id','=','permintaan_telemedicine.id_permintaan_telemedicine')
                    // ->where('rating.jenis_layanan','telemedicine')
                    ->groupBy('tenaga_medis_telemedicine.poli_id','tenaga_medis_telemedicine.nakes_id')
                    ->where('tenaga_medis_telemedicine.jenis_nakes', 'dokter')
                    ->where('tenaga_medis_telemedicine.poli_id', $request->poli_id)
                    ->get();
                if (count($dokter)>0) {
                    return Help::resApi('Berhasil',200,$dokter);
                } else {
                    return Help::resApi('Data Nakes tidak ',204);
                }

            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR GET LIST TENAGA MEDIS TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::resApi($validate->errors()->all()[0],400);
        }
    }

    public function getJadwalDokter(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'dokter_id' => 'required',
            'tanggal_kunjungan' => 'required',
        ],[
            'dokter_id.required' => 'Dokter Wajib Di isi',
            'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Di isi'
        ]);
        if (!$validate->fails()) {
            try{
                $kunjungan = date('w', strtotime($request->tanggal_kunjungan));
                $jadwal = JadwalTenagaMedis::select('id_jadwal_tenaga_medis', 'jam_awal', 'jam_akhir')
                            ->where('nakes_id', $request->dokter_id)
                            ->where('hari', $kunjungan)
                            ->where('jenis_pelayanan', 'telemedicine')
                            ->where('hari', $kunjungan)
                            ->get();
                if (count($jadwal)>0) {
                    return Help::resApi('Jadwal Dokter ditemukan',200,$jadwal);
                } else {
                    return Help::resApi('Jadwal Dokter tidak ditemukan',204);
                }

            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR GET LIST JADWAL TENAGA MEDIS TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::resApi($validate->errors()->all()[0],400);
        }
    }

    public function invoice(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required'
        ],[
            'id_permintaan_telemedicine.required' => 'ID Permintaan Telemedicine Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try{

            # Cari data permintaan berdasarkan id_permintaan_telemedicine
            if(!$permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->first()) {
                return Help::resApi('Permintaan Telemedicine Tidak Ditemukan',204);
            }

            if(in_array($permintaan->status_pasien, ['belum','menunggu'])) {
                return Help::resApi('Permintaan Belum Disetujui Petugas',204);
            }

            # Cari data payment berdasarkan permintaan_id dan jenis layanan telemedicine
            if (!$payment = PaymentPermintaan::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan', 'telemedicine')->first()) {
                return Help::resApi('Permintaan Belum Disetujui Petugas',204);
            }


            # Jika sudah memiliki invoice_id dari xendit, maka di carikan dengan getInvoice
            if (!$payment->invoice_id == "") {
                # Cek apakah ditolak oleh petugas
                if(in_array($permintaan->status_pasien,['tolak','batal']) || $permintaan->status_pembayaran == 'batal') {
                    $payment->status = 'EXPIRED';
                    $invoice = XenditHelpers::expiredInvoice($payment->invoice_id)->getData();
                }
                $invoice = XenditHelpers::getInvoice($payment->invoice_id)->getData();
                if ($invoice->metaData->code == 200) {
                    $payment->status = $invoice->response->status;
                    // if($payment)

                    $payment->save();
                    return Help::resApi('Berhasil mendapatkan invoice',200,$invoice->response->invoice_url);
                }
                if(!$invoice->metaData->code == 404) {
                    return Help::resApi('Terjadi kesalahan sistem',500);
                }
            }
            # Jika data payment dari xendit tidak ditemukan maka akan di buatkan ulang

            $date_exp = new DateTime($payment->tgl_expired);
            $date_now = new DateTime(date('Y-m-d H:i:s'));
            $date_diff = $date_exp->getTimestamp() - $date_now->getTimestamp();

            if(($date_diff)<0){
                return Help::resApi('Tanggal kadaluarsa sudah terlewat',400);
            }

            if(($date_diff)>(86400*3)){
                return Help::resApi('Tanggal kadaluarsa terlalu lama, tidak boleh melebihi h-3, mohon hubungi petugas',400);
            }

            # Buat invoice payment
            $items = array(
                (object)[
                    'name'=>'Telemedicine',
                    'price'=>(int)$permintaan->biaya_layanan,
                    'quantity'=>1
                ]
            );
            $new_invoice = XenditHelpers::createInvoice((string)$payment->id_payment_permintaan, 'Pembayaran Permintaan Layanan Telemedicine', $permintaan->biaya_layanan, (string)$date_diff, $items)->getData();
            if(!$new_invoice->metaData->code == 200) {
                return Help::resApi('Terjadi kesalahan sistem',500);
            }

            # Update Invoice ID di payment permintaan
            $payment->invoice_id = $new_invoice->response->id;
            $payment->status = $new_invoice->response->status;
            $payment->save();

            $activity = Activity::store(Auth::user()->id,'Create invoice permintaan telemedicine');

            return Help::resApi('Pembayaran berhasil dibuat',200,$new_invoice->response->invoice_url);

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR INVOICE TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }

    }

    public function saveRating(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required',
            'star_rating' => 'required|integer|between:1,5',
            'comments' => 'required',
        ],[
            'id_permintaan_telemedicine.required' => 'ID Permintaan Telemedicine Wajib Di isi',
            'star_rating.required' => 'Bintang Wajib Di isi',
            'star_rating.integer' => 'Bintang Wajib menggunakan angka',
            'star_rating.between' => 'Bintang Wajib bernilai 1 sampai 5',
            'comments.required' => 'Komentar Wajib Di isi',
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        DB::beginTransaction();

        try{
            if(!$permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->first()){
                return Help::resApi('Permintaan Telemedicine tidak ditemukan',204);
            }

            // if($permintaan->status_pasien != 'proses') {
            //     return Help::resApi('Tidak bisa memberikan penilaian saat tidak dalam pelayanan',400);
            // }

            if(!$permintaan_selesai = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->has('rekap_medik')->has('rekam_medis_lanjutan')->first()) {
                return Help::resApi('Belum bisa memberikan penilaian, tunggu sampai pelayanan dikerjakan oleh dokter dan perawat',400);
            }

            // if($resep = ResepObat::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan', 'telemedicine')->where('status_pembayaran', 'belum')->first()) {
            //     return Help::resApi('Belum bisa memberikan penilaian',400);
            // }

            if(!$vicon = VideoConference::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan', 'telemedicine')->first()) {
                return Help::resApi('Sistem tidak bisa menemukan riwayat pelayanan yang diminta',400);
            }

            if(date_diff(date_create(date('Y-m-d H:i:s',strtotime($vicon->waktu_selesai))),date_create(date('Y-m-d H:i:s')))->format('%a') >= 1) {
                return Help::resApi('Batas waktu penilaian sudah berakhir',400);
            }

            if($rating = Rating::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan', 'telemedicine')->first()){
                return Help::resApi('Tidak bisa mengubah penilaian yang sudah tersimpan',400);
            }

            $rating = new Rating;
            $rating->jenis_layanan = 'telemedicine';
            $rating->permintaan_id = $request->id_permintaan_telemedicine;
            $rating->star_rating = $request->star_rating;
            $rating->comments = $request->comments;

            if (!$rating->save()) {
                DB::rollback();
                return Help::resApi('Gagal menyimpan penilaian coba beberapa saat lagi',400);
            }

            # perubahan status di pindah ke menu selesaikan
            // $permintaan->status_pasien = 'selesai';
            // if (!$permintaan->save()) {
            //     DB::rollback();
            //     return Help::resApi('Gagal menyimpan penilaian coba beberapa saat lagi',400);
            // }

            if(!$activity = Activity::store(Auth::user()->id,'Rating telemedicine')) {
                DB::rollback();
                return Help::resApi('Gagal rating telemedicine',400);
            }

            DB::commit();
            return Help::resApi('Penilaian berhasil disimpan',200,$rating);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SAVE RATING TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function batalkanPermintaan(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required',
        ],[
            'id_permintaan_telemedicine.required' => 'ID Permintaan Telemedicine Wajib Di isi',
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        DB::beginTransaction();

        try {
            if (!$permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->first()) {
                DB::rollback();
                return Help::resApi('Permintaan Telemedicine tidak ditemukan',204);
            }

            if(!in_array($permintaan->status_pasien,['belum','menunggu','proses'])) {
                DB::rollback();
                return Help::resApi('Gagal membatalkan permintaan, Pasien dalam proses',400);
            }

            if(date('Y-m-d H:i:s', strtotime($permintaan->tanggal_kunjungan)) < date('Y-m-d H:i:s')) {
                DB::rollback();
                return Help::resApi('Gagal membatalkan permintaan, tanggal kunjungan sudah terlewat',400);
            }

            $permintaan->status_pasien = 'batal';
            $permintaan->status_pembayaran = 'batal';
            if($payment = PaymentPermintaan::where('permintaan_id', $request->in_permintaan_telemedicine)->where('jenis_layanan', 'telemedicine')->first()) {
                if($payment->invoice_id != '') {
                    $payment->status = 'EXPIRED';
                    $invoice = XenditHelpers::expiredInvoice($payment->invoice_id)->getData();
                }
                if(!$payment->save()) {
                    DB::rollback();
                    return Help::resApi('Gagal membatalkan permintaan, gagal mengubah status pembayaran',400);
                }
            }
            if(!$permintaan->save()){
                DB::rollback();
                return Help::resApi('Gagal membatalkan permintaan, gagal saat mengubah status',400);
            }
            if(!$activity = Activity::store(Auth::user()->id,'Batalkan telemedicine')) {
                DB::rollback();
                return Help::resApi('Gagal membatalkan telemedicine',400);
            }
            DB::commit();
            return Help::resApi('Permintaan berhasil dibatalkan',200);
        } catch (\Throwable $e) {
            DB::rollback();
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR BATALKAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getResep(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required'
        ],[
            'id_permintaan_telemedicine.required' => 'ID Permintaan Telemedicine Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }
        try {
            $permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id_permintaan_telemedicine)
            ->has('resep_obat')
            ->with('resep_obat')
            ->first();
            if(!$permintaan) {
                return Help::resApi('Permintaan / Resep tidak ditemukan',204);
            }

            $resep = ResepObat::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan', 'telemedicine');
            $resep->when($permintaan->resep_obat->status_pembayaran!='lunas', fn($q) =>
                $q->has('resep_obat_detail')->with('resep_obat_detail', function ($qq) {
                    $qq->selectRaw('(CASE WHEN (LENGTH(nama_obat)>=3) THEN CONCAT(SUBSTRING(nama_obat, 1, 2) , "****") WHEN (LENGTH(nama_obat)=2) THEN CONCAT(nama_obat, "****") ELSE CONCAT(nama_obat, "*****") END) AS nama_obat,resep_obat_id,kode_obat,qty,signa,harga');
                })
            );
            $resep->when($permintaan->resep_obat->status_pembayaran=='lunas', fn($q) =>
                $q->has('resep_obat_detail')->with('resep_obat_detail', function ($qq) {
                    $qq->selectRaw('nama_obat,resep_obat_id,kode_obat,qty,signa,harga');
                })
            );
            $resep = $resep->get();
            if(!$resep) {
                return Help::resApi('Resep Tidak Ditemukan',204);
            }
            return Help::resApi('Resep Ditemukan',200,$resep);

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET RESEP TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function invoiceResep(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required',
            'diantar' => 'required'
        ],[
            'id_permintaan_telemedicine.required' => 'ID Permintaan Telemedicine Wajib Di isi',
            'diantar.required' => 'Metode penerimaan obat Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        DB::beginTransaction();

        try{

            # Cari data permintaan berdasarkan id_permintaan_telemedicine
            if(!$permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->first()) {
                DB::rollback();
                return Help::resApi('Permintaan Telemedicine Tidak Ditemukan',204);
            }

            # Cari data payment berdasarkan permintaan_id dan jenis layanan telemedicine
            if (!$payment = PaymentPermintaan::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan', 'eresep_telemedicine')->first()) {
                if(in_array($permintaan->status_pasien,['selesai','batal','tolak'])) {
                    DB::rollback();
                    return Help::resApi('Tidak ada resep yang dibayar',204);
                }
                DB::rollback();
                return Help::resApi('Resep masih di proses',204);
            }

            # Jika sudah memiliki invoice_id dari xendit, maka di carikan dengan getInvoice
            if (!$payment->invoice_id == "") {
                # Cek apakah ditolak oleh petugas
                if(in_array($permintaan->status_pasien,['tolak','batal']) || $permintaan->status_pembayaran == 'batal') {
                    $payment->status = 'EXPIRED';
                    $invoice = XenditHelpers::expiredInvoice($payment->invoice_id)->getData();
                }
                $invoice = XenditHelpers::getInvoice($payment->invoice_id)->getData();
                if ($invoice->metaData->code == 200) {
                    $payment->status = $invoice->response->status;


                    $payment->save();
                    return Help::resApi('Berhasil mendapatkan invoice',200,$invoice->response->invoice_url);
                }
                if(!$invoice->metaData->code == 404) {
                    DB::rollback();
                    return Help::resApi('Terjadi kesalahan sistem',500);
                }
            }

            # Jika data payment dari xendit tidak ditemukan maka akan di buatkan ulang
            $date_exp = new DateTime($payment->tgl_expired);
            $date_now = new DateTime(date('Y-m-d H:i:s'));
            $date_diff = $date_exp->getTimestamp() - $date_now->getTimestamp();

            if(($date_diff)<0){
                DB::rollback();
                return Help::resApi('Tanggal kadaluarsa sudah terlewat',400);
            }

            if(($date_diff)>(86400*3)){
                DB::rollback();
                return Help::resApi('Tanggal kadaluarsa terlalu lama, tidak boleh melebihi h-3, mohon hubungi petugas',400);
            }

            if(!$resep = ResepObat::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan', 'telemedicine')->first()){
                DB::rollback();
                return Help::resApi('Tidak ditemukan resep',204);
            }

            $resepDetail = ResepObatDetail::where('resep_obat_id', $resep->id_resep_obat)->get();

            if(count($resepDetail) <= 0){
                DB::rollback();
                return Help::resApi('Tidak ada resep yang perlu dibayar',204);
            }

            $items = array();
            # Buat invoice payment
            foreach ($resepDetail as $key => $value) {
                $name = (strlen($value->nama_obat) > 2) ? (substr($value->nama_obat, 0, 2)."****") : $value->nama_obat."****";
                $newItem = (object)[
                    'name' => $value->kode_obat.$name,
                    'price' => (float)$value->harga,
                    'quantity' => (int)$value->qty
                ];
                $items[] = $newItem;
            }

            if($resep->diantar == "") {
                $resep->diantar = $request->diantar;
            }

            if(!$resep->save()) {
                DB::rollback();
                return Help::resApi('Gagal mendapatkan invoice',400);
            }

            if($request->diantar == 'tidak') {
                $total_bayar = $resep->total_bayar;
            } else {
                $total_bayar = $resep->total_bayar + $payment->ongkos_kirim;
                $items[] = (object)[
                    'name' => 'Ongkos Kirim',
                    'price' => (float)$payment->ongkos_kirim,
                    'quantity' => 1
                ];
            }
            $new_invoice = XenditHelpers::createInvoice((string)$payment->id_payment_permintaan, 'Pembayaran Eresep Telemedicine', $total_bayar, (string)$date_diff, $items)->getData();
            if(!$new_invoice->metaData->code == 200) {
                DB::rollback();
                return Help::resApi('Terjadi kesalahan sistem',500);
            }

            # Update Invoice ID di payment permintaan
            $payment->invoice_id = $new_invoice->response->id;
            $payment->status = $new_invoice->response->status;
            $payment->save();

            $activity = Activity::store(Auth::user()->id,'Create invoice eresep telemedicine');

            DB::commit();

            return Help::resApi('Pembayaran berhasil dibuat',200,$new_invoice->response->invoice_url);

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR INVOICE ERESEP TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function cekAntar(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required'
        ],[
            'id_permintaan_telemedicine.required' => 'ID Permintaan Telemedicine Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try {
            if(!$permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->first()) {
                return Help::resApi('Permintaan Telemedicine Tidak Ditemukan',204);
            }

            if(!$pengaturan = PengaturanTelemedicine::first()) {
                return Help::resApi('Tidak ditemukan pengaturan telemedicine, silahkan hubungi admin',204);
            }

            if((float)$permintaan->jarak > (float)$pengaturan->jarak_maksimal) {
                return Help::resApi('Jarak melebihi batas jangkauan pengiriman',400);
            }

            return Help::resApi('Jarak pengiriman dapat dijangkau sobat wahidin',200,ceil($permintaan->jarak) * (float)$pengaturan->biaya_per_km);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR CEK ANTAR TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function selesaikan(Request $request) {
        // return $per = PaymentPermintaan::with('permintaan_telemedicine')->get();
        // return $per = PermintaanTelemedicine::has('payment_permintaan')->with('payment_permintaan')->get();
        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required'
        ],[
            'id_permintaan_telemedicine.required' => 'ID Permintaan Telemedicine Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        DB::beginTransaction();

        try {
            if(!$permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->with('rekap_medik')->with('rekam_medis_lanjutan')->first()) {
                return Help::resApi('Tidak ditemukan telemedicine', 204);
            }

            if($permintaan->status_pasien != 'proses') {
                return Help::resApi('Gagal menyelesaikan telemedicine, permintaan tidak dalam pelayanan', 400);
            }

            if(!$permintaan->rekap_medik && !$permintaan->rekam_medis_lanjutan) {
                return Help::resApi('Gagal menyelesaikan telemedicine, permintaan masih dalam pengerjaan oleh tenaga kesehatan', 400);
            }

            $permintaan->status_pasien = 'selesai';
            if(!$permintaan->save()) {
                return Help::resApi('Gagal menyelesaikan telemedicine, coba beberapa saat lagi', 400);
            }
            if(!$activity = Activity::store(Auth::user()->id,'Selesaikan telemedicine')) {
                DB::rollback();
                return Help::resApi('Gagal menyelesaikan telemedicine',400);
            }

            DB::commit();
            return Help::resApi('Berhasil menyelesaikan telemedicine', 200);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SELESAIKAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    function tgl_indo($tanggal)
    { // ubah tanggal menjadi format indonesia
        $bulan = array (
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $pecahkan = explode('-', $tanggal);

        // variabel pecahkan 0 = tanggal
        // variabel pecahkan 1 = bulan
        // variabel pecahkan 2 = tahun

        return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }
}
