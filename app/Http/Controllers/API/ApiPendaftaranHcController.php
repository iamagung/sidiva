<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TmCustomer;
use App\Models\LayananHC;
use App\Models\PaketHC;
use App\Models\PermintaanHC;
use App\Models\PengaturanHC;
use App\Models\SyaratHC;
use App\Models\TenagaMedis;
use App\Models\TransaksiHC;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth, Hash;

class ApiPendaftaranHcController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getLayananHC(Request $request)
    {
        try {
            $data = LayananHC::all();
            if (count($data) > 0) {
                $respon = [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $data
                ];
            } else {
                $respon = [
                    'metaData' => [
                        "code" => 500,
                        "message" => 'Data Tidak Ditemukan'
                    ],
                    'response' => []
                ];
            }

            return response()->json($respon);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET LAYANAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getPaketHC(Request $request)
    {
        try{
            $data = PaketHC::all();
            if (count($data) > 0) {
                $respon = [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $data
                ];
            } else {
                $respon = [
                    'metaData' => [
                        "code" => 500,
                        "message" => 'Data Tidak Ditemukan'
                    ],
                    'response' => []
                ];
            }

            return response()->json($respon);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET PAKET HC ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getDetailPaketHC(Request $request)
    {
        try {
            if (!empty($request->paket_id)) {
                $data = PaketHC::where('id_paket_hc', $request->paket_id)->first();
                if (!empty($data)) {
                    $respon = [
                        'metaData' => [
                            "code" => 200,
                            "message" => 'Berhasil'
                        ],
                        'response' => $data
                    ];
                } else {
                    $respon = [
                        'metaData' => [
                            "code" => 201,
                            "message" => 'Data Tidak Ditemukan.'
                        ],
                        'response' => []
                    ];
                }
            } else {
                $respon = [
                    'metaData' => [
                        "code" => 500,
                        "message" => 'Terjadi Kesalahan Sistem'
                    ],
                    'response' => []
                ];
            }
            return response()->json($respon);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET DETAIL PAKET HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getSyaratAturanHC(Request $request)
    {
        try{
            $data = SyaratHC::where('id_syarat_hc', 1)->first();
            if (!empty($data)) {
                $respon = [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $data
                ];
            } else {
                $respon = [
                    'metaData' => [
                        "code" => 500,
                        "message" => 'Data Tidak Ditemukan'
                    ],
                    'response' => []
                ];
            }

            return response()->json($respon);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET SYARAT & ATURAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function pesanJadwalHC(Request $request)
    {
        if ($request->no_rm == "") {
            $validate = Validator::make($request->all(),[
                'layanan_id' => 'required',
                'paket_id' => 'required',
                'nik' => 'required',
                'nama' => 'required',
                'tanggal_lahir' => 'required',
                'tanggal_kunjungan' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'jenis_kelamin' => 'required',
                'alamat'=> 'required'
            ],[
                'layanan_id.required' => 'Layanan Home Care Wajib Di isi',
                'paket_id.required' => 'Paket Home Care Wajib Di isi',
                'nik.required' => 'Nik Wajib Di isi',
                'nama.required' => 'Nama Wajib Di isi',
                'tanggal_lahir.required' => 'Tanggal Lahir Wajib Di isi',
                'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Di isi',
                'latitude.required' => 'Lokasi Wajib Diisi',
                'longitude.required' => 'Lokasi Wajib Diisi',
                'jenis_kelamin.required' => 'Jenis Kelamin Wajib Di isi',
                'alamat.required' => 'Alamat Wajib Di isi'
            ]);
        }else{
            $validate = Validator::make($request->all(),[
                'no_rm' => 'required',
                'jenis_pembayaran' => 'required',
                'layanan_id' => 'required',
                'paket_id' => 'required',
                'tanggal_kunjungan' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            ],[
                'no_rm.required' => 'No RM Wajib Diisi',
                'jenis_pembayaran.required' => 'Jenis Pembayaran Wajib Di isi',
                'layanan_id.required' => 'Layanan Home Care Wajib Di isi',
                'paket_id.required' => 'Paket Home Care Wajib Di isi',
                'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Di isi',
                'latitude.required' => 'Lokasi Wajib Diisi',
                'longitude.required' => 'Lokasi Wajib Diisi'
            ]);
        }
        if (!$validate->fails()) {
            $tanggal = strtotime($request->tanggal_kunjungan);
            $request->tanggal_kunjungan = date('Y-m-d', $tanggal);
            $check_nik = $this->checkByNik($request->nik, $request->tanggal_kunjungan);
            if ($check_nik > 0) {
                return response()->json([
                    'metadata' => [
                        'message' => 'Nik telah digunakan untuk mendaftar pada tanggal '.$this->tgl_indo($request->tanggal_kunjungan),
                        'code'    => 201,
                    ],
                    'response' => [],
                ]);
            }
			$timeCur = date('H:i');
			$dateCur = date('Y-m-d');
			$dayName = date('D',strtotime($request->tanggal_kunjungan));
            $pengaturan = PengaturanHC::where('id_pengaturan_hc', 1)->first();
            if ($dayName == 'Mon') { # Senin
                if ($pengaturan->seninBuka == '' || $pengaturan->seninTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->seninBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->seninBuka,201);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->seninTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->seninTutup,201);
                    }
                }
            }
            if ($dayName == 'Tue') { # Selasa
                if ($pengaturan->selasaBuka == '' || $pengaturan->selasaTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->selasaBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->selasaBuka,201);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->selasaTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->selasaTutup,201);
                    }
                }
            }
            if ($dayName == 'Wed') { # Rabu
                if ($pengaturan->rabuBuka == '' || $pengaturan->rabuTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->rabuBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->rabuBuka,201);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->rabuTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->rabuTutup,201);
                    }
                }
            }
            if ($dayName == 'Thu') { # Kamis
                if ($pengaturan->kamisBuka == '' || $pengaturan->kamisTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->kamisBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->kamisBuka,201);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->kamisTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->kamisTutup,201);
                    }
                }
            }
            if ($dayName == 'Fri') { # Jum'at
                if ($pengaturan->jumatBuka == '' || $pengaturan->jumatTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->jumatBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->jumatBuka,201);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->jumatTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->jumatTutup,201);
                    }
                }
            }
            if ($dayName == 'Sat') { # Sabtu
                if ($pengaturan->sabtuBuka == '' || $pengaturan->sabtuTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->sabtuBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->sabtuBuka,201);
                    } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->sabtuTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->sabtuTutup,201);
                    }
                }
            }
			if($dayName == 'Sun'){ # If tanggal periksa adalah hari minggu
				return Help::resApi('Tidak bisa mengambil antrian pada hari minggu.',201);
			}
            if($request->tanggal_kunjungan<$dateCur){ # If tanggal periksa kemarin{back date}
                return Help::resApi('Tanggal sudah terlewat.',201);
            }
            $pasien = TmCustomer::where('NoKtp','=',$request->nik)->first();
            if (empty($pasien)) {
                $pasien = TmCustomer::where('KodeCust','=',$request->no_rm)->first();
            }
            if (strlen($request->nik)!=16) {
                return Help::resApi('NIK tidak sesuai standar 16 digit.',201);
            }
            if($request->jenis_pembayaran == 'BPJS') {
                if (strlen($request->no_bpjs)!=13) {
                    return Help::resApi('Nomor BPJS tidak sesuai standar 13 digit.',201);
                }
            }
            try {
                $noreg = Help::generateNoRegHc($request);
                $data = new PermintaanHC;
                $data->layanan_hc_id     = $request->layanan_id;
                $data->paket_hc_id       = $request->paket_id;
                $data->no_rm             = !empty($pasien) ? $request->no_rm : null;
                $data->nik               = !empty($pasien) ? $pasien->NoKtp : $request->nik;
                $data->no_registrasi     = $noreg;
                $data->nama              = !empty($pasien) ? $pasien->NamaCust : strtoupper($request->nama);
                $data->alamat            = !empty($pasien) ? $pasien->Alamat : $request->alamat;
                $data->no_bpjs           = !empty($request->no_bpjs) ? $request->no_bpjs : null;
                $data->no_rujukan        = !empty($request->no_rujukan) ? $request->no_rujukan : null;
                $data->tanggal_order     = date('Y-m-d');
                $data->tanggal_kunjungan = $request->tanggal_kunjungan;
                $data->tanggal_lahir     = !empty($pasien) ? $pasien->TglLahir : $request->tanggal_lahir;
                $data->alergi_pasien     = $request->alergi_pasien;
                $data->latitude          = $request->latitude;
                $data->longitude         = $request->longitude;
                $data->jenis_pembayaran  = $request->jenis_pembayaran;
                $data->no_telepon        = !empty($pasien) ? $pasien->Telp : $request->no_telepon;
                $data->jenis_kelamin     = !empty($pasien) ? $pasien->JenisKel : $request->jenis_kelamin;
                $data->status_pasien     = 'belum';
                $data->save();

                if ($data) {
                    # Update Biaya layanan & biaya ke lokasi
                    $biayaPerKm = DB::table('pengaturan_hc')->where('id_pengaturan_hc', 1)->first()->biaya_per_km;
                    $distance   = Help::calculateDistance($data->latitude, $data->longitude);
                    $upData     = PermintaanHC::where('id_permintaan_hc', $data->id_permintaan_hc)->first();
                    $upData->biaya_layanan   = PaketHC::where('id_paket_hc', $data->paket_hc_id)->first()->harga;
                    $upData->biaya_ke_lokasi = (int)$biayaPerKm * (int)$distance;
                    $upData->save();

                    return response()->json([
                        'metadata' => [
                            'message' => 'Success',
                            'code'    => 200,
                        ],
                        'response' => Help::callbackRegistHc($data),
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
                $log = ['ERROR PENDAFTARAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
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

    // public function getListPembayaranHC($id)
    // {
    //     try{
    //         $permintaan = PermintaanHC::where('id_permintaan_hc', $id)->first();
    //         $paket      = PaketHC::where('id_paket_hc', $permintaan->paket_hc_id)->first();
            
    //         $data       = [
    //             'permintaan' => $permintaan,
    //             'paket'      => $paket
    //         ];

    //         if (!empty($permintaan)) {
    //             return [
    //                 'metaData' => [
    //                     "code" => 200,
    //                     "message" => 'Berhasil'
    //                 ],
    //                 'response' => $data
    //             ];
    //         } else {
    //             return [
    //                 'metaData' => [
    //                     "code" => 201,
    //                     "message" => 'Gagal.'
    //                 ],
    //                 'response' => []
    //             ];
    //         }

    //     } catch (\Throwable $e) {
    //         # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
    //         $log = ['ERROR GET LIST PEMBAYARAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
    //         Help::logging($log);

    //         return Help::resApi('Terjadi kesalahan sistem',500);
    //     }
    // }

    // public function getInvoiceHC($id)
    // {
    //     try{
    //         $permintaan = PermintaanHC::where('id_permintaan', $id)->first();
    //         $pasien = DB::connection('dbrsud')->table('tm_customer')->where('NoKtp', $permintaan->nik)->first();
    //         $id_layanan = explode(",", $permintaan->layanan_id);
    //         $layanan = DB::table('layanan_mcu')->whereIn('id_layanan', $id_layanan)->get();
    //         $sum = 0;
    //         // return $layanan;
    //         for($i = 0; $i < count($layanan); $i++){
    //             $sum += $layanan[$i]->harga;
    //         }
    //         # update to permintaan_mcu
    //         $permintaan->biaya = $sum;
    //         $permintaan->metode_pembayaran = 'Tunai';
    //         $permintaan->save();
    //         # insert to transaksi_mcu
    //         $transaksi = new TransaksiMCU;
    //         $transaksi->id_permintaan_mcu = $id;
    //         $transaksi->nominal           = $sum;
    //         $transaksi->invoice           = "INV/".date('Ymd')."/HOMECARE"."/".rand(20, 200);
    //         $transaksi->status            = 'pending';
    //         $transaksi->save();

    //         $data = [
    //             'permintaan'    => $permintaan,
    //             'pasien'        => $pasien,
    //             'layanan'       => $layanan,
    //             'transaksi'     => $transaksi 
    //         ];
    //         if ($permintaan) {
    //             return [
    //                 'metaData' => [
    //                     "code" => 200,
    //                     "message" => 'Berhasil'
    //                 ],
    //                 'response' => $data
    //             ];
    //         } else {
    //             return [
    //                 'metaData' => [
    //                     "code" => 201,
    //                     "message" => 'Gagal.'
    //                 ],
    //                 'response' => []
    //             ];
    //         }

    //     } catch (\Throwable $e) {
    //         # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
    //         $log = ['ERROR GET LIST PEMBAYARAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
    //         Help::logging($log);

    //         return Help::resApi('Terjadi kesalahan sistem',500);
    //     }
    // }

    public function selesaikanPelayananHC(Request $request)
    {
        try {
            $data = PermintaanHC::where('id_permintaan_hc', $request->id_permintaan_hc)->first();
            $data->status_pasien = 'selesai';
            $data->save();
            if ($data) {
                $respon = [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $data
                ];
            } else {
                $respon = [
                    'metaData' => [
                        "code" => 500,
                        "message" => 'Terjadi kesalahan sistem'
                    ],
                    'response' => []
                ];
            }

            return response()->json($respon);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SELESAIKAN PELAYANAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function batalOtomatisPermintaanHC(Request $request)
    {
        try {
            $data = PermintaanHC::where('id_permintaan_hc', $request->id_permintaan_hc)->first();
            $data->status_pasien = 'batal';
            $data->save();
            if ($data) {
                $respon = [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $data
                ];
            } else {
                $respon = [
                    'metaData' => [
                        "code" => 500,
                        "message" => 'Terjadi kesalahan sistem'
                    ],
                    'response' => []
                ];
            }

            return response()->json($respon);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SELESAIKAN PELAYANAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getPermintaanTM($id)
    {
        try{
            $user = DB::connection('dbapm')->table('users')->where('id', $id)->first();
            $tm = TenagaMedis::where('kode_dokter', $user->kode_dokter)->first();
            if (!empty($tm)) {
                $permintaan = PermintaanHC::where('tenaga_medis_id', $tm->id_tenaga_medis)
                ->where('tanggal_kunjungan', date('Y-m-d'))
                ->get();
            } else {
                return [
                    'metaData' => [
                        "code" => 201,
                        "message" => 'Tenaga Medis tidak ditemukan.'
                    ],
                    'response' => []
                ];
            }
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
                        "code" => 201,
                        "message" => 'Tidak ada pasien.'
                    ],
                    'response' => []
                ];
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET PERMINTAAN TM ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function riwayatPermintaanTM(Request $request)
    {
        try{
            $permintaan = PermintaanHC::where('nik', $request->nik)
                ->whereIn('status_pasien', ['proses', 'selesai'])
                ->orderBy('id_permintaan_hc', 'DESC')
                ->get();
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
                        "code" => 201,
                        "message" => 'Tidak ada riwayat.'
                    ],
                    'response' => []
                ];
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR RIWAYAT PERMINTAAN TM ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getProfileTM(Request $request)
    {
        try {
            $data = DB::connection('dbapm')->table('users')->where('id', $request->id)->first();

            if (!empty($data)) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Data Ditemukan'
                    ],
                    'response' => $data
                ];
            } else {
                return [
                    'metaData' => [
                        "code" => 201,
                        "message" => 'Data Tidak Ditemukan'
                    ],
                    'response' => []
                ];
            }
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET USER TM ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function updateProfileTM(Request $request)
    {
        try {
            $getUser = DB::connection('dbapm')->table('users')->where('id', $request->id)->first();
            if ($request->password != '') {
                $data = [
                    'name_user' => $request->nama,
                    'email'     => $request->username,
                    'password'  => Hash::make($request->password),
                    'phone'     => $request->telp,
                ];
            } else {
                $data = [
                    'name_user' => $request->nama,
                    'email'     => $request->username,
                    'password'  => Hash::make($getUser->password),
                    'phone'     => $request->telp,
                ];
            }
            $updateUser = DB::connection('dbapm')->table('users')->where('id', $request->id)->update($data);
            if($updateUser){
                $respon = [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil Update Data'
                    ],
                    'response' => DB::connection('dbapm')->table('users')->where('id', $request->id)->first()
                ];
            }else{
                $respon = [
                    'metaData' => [
                        "code" => 201,
                        "message" => 'Gagal Update Data'
                    ],
                    'response' => []
                ];
            }
            return $respon;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR UPDATE USER TM ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function saveRatingHC(Request $request)
    {
        try{
            $data = [
                'permintaan_hc_id' => $request->id_permintaan_hc,
                'comments'          => $request->comments,
                'star_rating'       => $request->rating
            ];
            $rating = DB::table('rating_hc')->insert($data);

            if ($rating) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => DB::table('rating_hc')->where('permintaan_hc_id', $request->id_permintaan_hc)->first()
                ];
            } else {
                return [
                    'metaData' => [
                        "code" => 201,
                        "message" => 'Gagal.'
                    ],
                    'response' => []
                ];
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SAVE RATING HC ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    // public function getPaymentHC(Request $request)
    // {
    //     try {
    //         // $metode = $this->tripay->initChannelPembayaran()->getData();
    //         $metode = $this->tripay->initChannelPembayaran()->getData()[0]->payment;
    //         if ($metode) {
    //             $respon = [
    //                 'metaData' => [
    //                     "code" => 200,
    //                     "message" => 'Berhasil'
    //                 ],
    //                 'response' => $metode
    //             ];
    //         } else {
    //             $respon = [
    //                 'metaData' => [
    //                     "code" => 500,
    //                     "message" => 'Data Tidak Ditemukan'
    //                 ],
    //                 'response' => []
    //             ];
    //         }

    //         return response()->json($respon);
    //     } catch (\Throwable $e) {
    //         # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
    //         $log = ['ERROR GET PAYMENT HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
    //         Help::logging($log);

    //         return Help::resApi('Terjadi kesalahan sistem',500);
    //     }
    // }

    // public function transaksiProcessHC(Request $request)
    // {
    //     # initial request
    //     $id_permintaan_hc = $request->id_permintaan_hc;
    //     $nominal = $request->nominal;
    //     $metode = $request->metode;

    //     # insert to db local
    //     $transaksi = new TransaksiHC;
    //     $transaksi->id_permintaan_hc = $id_permintaan_hc;
    //     $transaksi->nominal          = $nominal;
    //     $transaksi->invoice          = "INV/".date('Ymd')."/HOMECARE"."/".rand(20, 200);
    //     $transaksi->save();

    //     # get permintaan homecare
    //     $permintaanHC = PermintaanHC::where('id_permintaan_hc', $id_permintaan_hc)->first();
    //     $merchantRef = $transaksi->invoice;
    //     $init = $this->tripay->initTransaction($merchantRef);
    //     $init->setAmount($transaksi->nominal); // for close payment
    //     // $init->setMethod('BNIVA'); // for open payment

    //     $signature = $init->createSignature();

    //     $transaction = $init->closeTransaction(); // define your transaction type, for close transaction use `closeTransaction()`
    //     $transaction->setPayload([ # persiapan data untuk dikirim ke merchant
    //     // $transaction->setPayload([
    //         'method'            => $metode,
    //         'merchant_ref'      => $merchantRef,
    //         'amount'            => $init->getAmount(),
    //         'customer_name'     => $permintaanHC->nama,
    //         // 'customer_email'    => $permintaanHC->email,
    //         'customer_phone'    => $permintaanHC->no_telepon,
    //         'order_items'       => [
    //             [
    //                 'sku'       => 'PELAYANANHC',
    //                 'name'      => 'Pelayanan Home Care',
    //                 'price'     => $init->getAmount(),
    //                 'quantity'  => 1
    //             ]
    //         ],
    //         'callback_url'      => 'https://namadomain.com/callback', // url api callback
    //         'return_url'        => 'https://namadomain.com/redirect', // redirect ke halaman pembayaran
    //         'expired_time'      => (time()+(24*60*60)), // 24 jam
    //         'signature'         => $init->createSignature()
    //     ]); // set your payload, with more examples https://tripay.co.id/developer

    //     $getPayload = $transaction->getPayload();
    //     $get_data_from_server = $transaction->getJson();

    //     return redirect($get_data_from_server->data->checkout_url); // redirect halaman ke halaman pembayaran
    // }

    // public function callbackHC(Request $request)
    // {
    //     $init = $this->tripay->initCallback();
    //     $result = $init->getJson(); // get json callback

    //     if ($request->header("X-Callback-Event") != "payment_status") {
    //         die("Akses dilarang");
    //     }

    //     $transaksi = TransaksiHC::where('invoice', $result->merchant_ref)->first();
    //     if ($transaksi) { # pengecekan apakah ada transaksi
    //         if ($result->status == "PAID") { # pengecekan apakah transaksi sudah dibayar
    //             $transaksi->status == "PAID";
    //         }

    //         $transaksi->status = $result->status;
    //         $transaksi->update();

    //         return response()->json($result);
    //     }

    //     return response()->json(['message' => "Transaksi tidak ditemukan"]);
    // }

    public function checkByNik($nik, $tanggal) //check nik apakah sudah digunakan
    {
        $check = PermintaanHC::where('nik','=',$nik)->where('tanggal_kunjungan','=',$tanggal)->count();
        return $check;
    }

    function tgl_indo($tanggal){ // ubah tanggal menjadi format indonesia
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
