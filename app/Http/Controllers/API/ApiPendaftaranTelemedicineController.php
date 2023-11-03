<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PermintaanTelemedicine;
use App\Models\PengaturanTelemedicine;
use App\Models\TenagaMedisTelemedicine;
use App\Models\JadwalTenagaMedis;
use App\Models\TmCustomer;
use App\Models\DBRSUD\TmPoli;
use Illuminate\Http\Request;
use App\Helpers\Helpers as Help;
use App\Helpers\XenditHelpers;
use Validator, DB;

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
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'poli_id' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'keluhan' => 'required',
            'biaya_layanan' => 'required',
            'no_telepon' => 'required',
            'jadwal_dokter' => 'required|regex:/\d{2}:\d{2}-\d{2}:\d{2}/',
            'tenaga_medis_id' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ],[
            'nik.required' => 'Nik Wajib Di isi',
            'nama.required' => 'Nama Wajib Di isi',
            'tempat_lahir.required' => 'Tempat Lahir Wajib Di isi',
            'tanggal_lahir.required' => 'Tanggal Lahir Wajib Di isi',
            'tanggal_lahir.date' => 'Tanggal Lahir harus berformat yyyy-mm-dd',
            'alamat.required' => 'Alamat Wajib Di isi',
            'poli_id.required' => 'Poli Wajib Di isi',
            'keluhan.required' => 'Keluhan  Wajib Di isi',
            'biaya_layanan.required' => 'Biaya Layanan  Wajib Di isi',
            'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Di isi',
            'tanggal_kunjungan.date' => 'Tanggal Kunjungan harus berformat yyyy-mm-dd',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Di isi',
            'no_telepon.required' => 'No. telepon Wajib Di isi',
            'jadwal_dokter.required' => 'Jadwal Dokter Wajib Di isi',
            'tenaga_medis_id.required' => 'Tenaga Medis Wajib Di isi',
            'jadwal_dokter.regex' => 'Jadwal Dokter Harus Menggunakan Format H:i-H:i, cth:(08:00-09:00)',
            'longitude.required' => 'Lokasi Pasien (Longitude) Wajib Di isi',
            'latitude.required' => 'Lokasi Pasien (Latitude) Wajib Di isi',
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
                $data->keluhan              = $request->keluhan;
                $data->metode_pembayaran    = $request->metode_pembayaran ? $request->metode_pembayaran : null;
                $data->status_pasien        = 'belum';
                $data->status_pembayaran    = 'belum';
                // return $data;
                $data->save();

                if ($data) {
                    return response()->json([
                        'metadata' => [
                            'message' => 'Success',
                            'code'    => 200,
                        ],
                        'response' => Help::callbackRegistTelemedicine($data),
                    ]);
                }else{
                    return response()->json([
                        'metadata' => [
                            'message' => 'Error',
                            'code'    => 400,
                        ],
                        'response' => [],
                    ]);
                }
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR PENDAFTARAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 400,
                ],
                'response' => [],
            ]);
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
                    ->get();
                // $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order as tanggal_transaksi', 'tm_poli.NamaPoli as nama_poli', 'nakes_user.name as nama_nakes', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'biaya_layanan as total_biaya')
                //     // ->join(DB::connection('dbrsud')->raw('dbsimars_baru.tm_poli as tm_poli'),'permintaan_telemedicine.poli_id','=','tm_poli.KodePoli')
                //     // ->join('tenaga_medis_telemedicine as nakes', 'permintaan_telemedicine.tenaga_medis_id', '=', 'nakes.nakes_id')
                //     ->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'nakes.nakes_id','=','nakes_user.id')
                //     ->join('users_android as ua', 'permintaan_telemedicine.nik', '=', 'ua.nik')
                //     ->where('ua.user_id', '=', $request->id_user)
                //     ->get();
                if (count($permintaan)>0) {
                    return [
                        'metaData' => [
                            "code" => 200,
                            "message" => 'Berhasil'
                        ],
                        'response' => $permintaan
                    ];
                } else {
                    return [
                        'metaData' => [
                            "code" => 204,
                            "message" => 'List Permintaan Tidak ditemukan'
                        ],
                        'response' => []
                    ];
                }

            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR GET LIST POLI TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 400,
                ],
                'response' => [],
            ]);
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
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 400,
                ],
                'response' => [],
            ]);
        }
        try{
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order as tanggal_transaksi', 'poli_id', 'tenaga_medis_id', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'biaya_layanan as total_biaya')
                ->with('tmPoli:KodePoli,NamaPoli as nama_poli')
                ->has('dokter')
                ->with('dokter', function($q) {
                    $q->select('nakes_id')->has('user_ranap')->with('user_ranap:id,name as nama_dokter');
                })
                ->whereHas('user_android',function ($q) use($request) {
                    $q->where('user_id', '=', $request->id_user);
                })
                ->where('status_pasien', 'selesai')
                ->orderBy('id_permintaan_telemedicine', 'DESC')
                ->get();
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
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $permintaan
                ];
            } else {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Tidak ada riwayat.'
                    ],
                    'response' => []
                ];
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
            // $poli = DB::connection('dbrsud')->table('tm_poli')->select('KodePoli','NamaPoli')->get();
            $poli = TmPoli::select('KodePoli','NamaPoli')
                ->join('mapping_poli_bridging as mpd', 'tm_poli.KodePoli', '=', 'mpd.kdpoli_rs')
                ->get();
            if (count($poli) > 0) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $poli
                ];
            } else {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Data Poli tidak ditemukan!'
                    ],
                    'response' => []
                ];
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
            'poli_id' => 'required'
        ],[
            'poli_id.required' => 'Poli Wajib Di isi'
        ]);
        if (!$validate->fails()) {
            try{
                $dokter = TenagaMedisTelemedicine::select('poli_id','nakes_id','tenaga_medis_telemedicine.tarif')
                    ->whereHas('jadwalMedis', function ($q) {
                        $q->where('jenis_pelayanan', 'telemedicine');
                    })
                    ->with('tmPoli:KodePoli,NamaPoli as nama_poli')
                    ->with('user_ranap:id,name as nama_dokter')
                    ->with(['permintaan' => function ($q) {
                        $q->where('status_pasien','selesai')
                        ->whereHas('rating');
                    }])
                    ->where('tenaga_medis_telemedicine.jenis_nakes', 'dokter')
                    ->where('poli_id', $request->poli_id)
                    ->get();
                if (count($dokter)>0) {
                    return [
                        'metaData' => [
                            "code" => 200,
                            "message" => 'Berhasil'
                        ],
                        'response' => $dokter
                    ];
                } else {
                    return [
                        'metaData' => [
                            "code" => 204,
                            "message" => 'Data Nakes tidak ditemukan'
                        ],
                        'response' => []
                    ];
                }

            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR GET LIST TENAGA MEDIS TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 400,
                ],
                'response' => [],
            ]);
        }
    }

    public function getJadwalDokter(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'dokter_id' => 'required'
        ],[
            'dokter_id.required' => 'Dokter Wajib Di isi'
        ]);
        if (!$validate->fails()) {
            try{
                $jadwal = JadwalTenagaMedis::select('id_jadwal_tenaga_medis', 'jam_awal', 'jam_akhir')->where('nakes_id', $request->dokter_id)->where('jenis_pelayanan', 'telemedicine')->get();
                if (count($jadwal)>0) {
                    return [
                        'metaData' => [
                            "code" => 200,
                            "message" => 'Jadwal Dokter Ditemukan'
                        ],
                        'response' => $jadwal
                    ];
                } else {
                    return [
                        'metaData' => [
                            "code" => 204,
                            "message" => 'Jadwal Dokter Tidak Ditemukan'
                        ],
                        'response' => []
                    ];
                }

            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR GET LIST JADWAL TENAGA MEDIS TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 400,
                ],
                'response' => [],
            ]);
        }
    }

    public function tes() {
        XenditHelpers::buatCharge();
        // return XenditHelpers::setKey();

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
