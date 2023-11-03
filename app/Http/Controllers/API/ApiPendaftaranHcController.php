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
use App\Models\User;
use App\Models\LayananPermintaanHc;
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
                return Help::custom_response(200, "success", "Ok", $data);
            }
            return Help::custom_response(500, "error", "data tidak ditemukan", null);
        } catch (\Throwable $e) {
            $log = ['ERROR GET LAYANAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function getDetailLayananHC(Request $request)
    {
        try {
            if(!$request->id_layanan_hc){ # wajib send param id layanan
                return Help::custom_response(500, "error", "id layanan wajib diisi", null);
            }
            $data = LayananHC::where('id_layanan_hc', $request->id_layanan_hc)->first();
            if(!$data){ #Jika tidak ada data
                return Help::custom_response(500, "error", "data tidak ditemukan", null);
            }
            return Help::custom_response(200, "success", "Ok", $data);
        } catch (\Throwable $e) {
            $log = ['ERROR GET DETAIL PAKET HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function getSyaratAturanHC(Request $request) {
        try{
            $data = SyaratHC::where('id_syarat_hc', 1)->first();
            if (!$data) {
                return Help::custom_response(500, "error", "data tidak ditemukan", null);
            }
            return Help::custom_response(200, "success", "Ok", $data);
        } catch (\Throwable $e) {
            $log = ['ERROR GET SYARAT & ATURAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function pesanJadwalHC(Request $request) {
        $validate = Validator::make($request->all(),[
            'nik' => 'required',
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'telepon' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'layanan_id' => 'required',
            'tanggal_kunjungan' => 'required',
            'waktu_layanan' => 'required'
        ],[
            'nik.required' => 'NIK Wajib Diisi',
            'nama.required' => 'Nama Lengkap Wajib Di isi',
            'tempat_lahir.required' => 'Tempat Lahir Wajib Di isi',
            'tanggal_lahir.required' => 'Tanggal Lahir Wajib Di isi',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Di isi',
            'alamat.required' => 'Alamat Wajib Di isi',
            'telepon.required' => 'Telepon Wajib Di isi',
            'latitude.required' => 'Lokasi Wajib Diisi',
            'longitude.required' => 'Lokasi Wajib Diisi',
            'layanan_id.required' => 'Jenis Layanan Wajib Di isi',
            'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Diisi',
            'waktu_layanan.required' => 'Waktu Layanan Wajib Diisi'
        ]);
        if (!$validate->fails()) {
			$day = date('D');
            $timeCur = date('H:i');
            $tanggal = date('Y-m-d');
            $tanggalKunjungan = date('Y-m-d', strtotime($request->tanggal_kunjungan));
            if($tanggalKunjungan<$tanggal){# Jika tanggal periksa kemarin{back date}
                return Help::resApi('Tanggal sudah terlewat.',500);
            }
            # Menghitung selisih hari antara tanggal order dan tanggal pendaftaran
            $selisih_hari = strtotime($tanggal) - strtotime($tanggalKunjungan);
            $selisih_hari = $selisih_hari / (60 * 60 * 24); # Menghitung selisih hari
            if ($selisih_hari > -1 || $selisih_hari < -3) { #Pengecekan pendaftaran hanya bisa (H-3) - (H-1)
                return Help::resApi('Pendaftaran bisa dilakukan H-3 sampai H-1',500);
            }
            if ($check_nik = $this->checkByNik($request->nik, $tanggalKunjungan) > 0) {# pengecekan agar tidak dobel
                return Help::custom_response(500, "error", 'Nik telah digunakan untuk mendaftar pada tanggal '.$this->tgl_indo($tanggalKunjungan), null);
            }
            $pengaturan = PengaturanHC::where('id_pengaturan_hc', 1)->first();
            if ($day == 'Mon') { # Senin
                if ($pengaturan->seninBuka == '' || $pengaturan->seninTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->seninBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->seninBuka,201);
                    } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->seninTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->seninTutup,201);
                    }
                }
            }
            if ($day == 'Tue') { # Selasa
                if ($pengaturan->selasaBuka == '' || $pengaturan->selasaTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->selasaBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->selasaBuka,201);
                    } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->selasaTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->selasaTutup,201);
                    }
                }
            }
            if ($day == 'Wed') { # Rabu
                if ($pengaturan->rabuBuka == '' || $pengaturan->rabuTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->rabuBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->rabuBuka,201);
                    } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->rabuTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->rabuTutup,201);
                    }
                }
            }
            if ($day == 'Thu') { # Kamis
                if ($pengaturan->kamisBuka == '' || $pengaturan->kamisTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->kamisBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->kamisBuka,201);
                    } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->kamisTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->kamisTutup,201);
                    }
                }
            }
            if ($day == 'Fri') { # Jum'at
                if ($pengaturan->jumatBuka == '' || $pengaturan->jumatTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->jumatBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->jumatBuka,201);
                    } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->jumatTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->jumatTutup,201);
                    }
                }
            }
            if ($day == 'Sat') { # Sabtu
                if ($pengaturan->sabtuBuka == '' || $pengaturan->sabtuTutup == '') { # If Tidak ada jadwal
                    return Help::resApi('Tidak ada jadwal hari ini.',201);
                } else {
                    if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->sabtuBuka) {
                        return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->sabtuBuka,201);
                    } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->sabtuTutup) {
                        return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->sabtuTutup,201);
                    }
                }
            }
			if($day == 'Sun'){ # If tanggal periksa adalah hari minggu
				return Help::resApi('Tidak bisa mengambil antrian pada hari minggu.',201);
			}
            $pasien = TmCustomer::where('NoKtp','=',$request->nik)->first();
            if (strlen($request->nik)!=16) {
                return Help::resApi('NIK tidak sesuai standar 16 digit.',201);
            }
            try {
                DB::beginTransaction();
                $data = new PermintaanHC;
                $data->no_rm             = !empty($pasien) ? $request->no_rm : null;
                $data->nik               = !empty($pasien) ? $pasien->NoKtp : $request->nik;
                $data->no_registrasi     = Help::generateNoRegHc($request);
                $data->nama              = !empty($pasien) ? $pasien->NamaCust : strtoupper($request->nama);
                $data->alamat            = !empty($pasien) ? $pasien->Alamat : $request->alamat;
                $data->tempat_lahir      = $request->tempat_lahir;
                $data->tanggal_lahir     = !empty($pasien) ? date('Y-m-d', strtotime($pasien->TglLahir)) : date('Y-m-d', strtotime($request->tanggal_lahir));
                $data->tanggal_order     = $tanggal;
                $data->tanggal_kunjungan = $tanggalKunjungan;
                $data->alergi_pasien     = $request->alergi_pasien;
                $data->latitude          = $request->latitude;
                $data->longitude         = $request->longitude;
                $data->jenis_pembayaran  = $request->jenis_pembayaran;
                $data->no_telepon        = !empty($pasien) ? $pasien->Telp : $request->telepon;
                $data->jenis_kelamin     = !empty($pasien) ? $pasien->JenisKel : $request->jenis_kelamin;
                $data->waktu_layanan     = $request->waktu_layanan;
                $data->status_pasien     = 'belum';
                $data->status_pembayaran = 'pending';
                # Start calculate
                $biayaPerKm = DB::table('pengaturan_hc')->where('id_pengaturan_hc', 1)->first()->biaya_per_km;
                $distance   = Help::calculateDistance($request->latitude,$request->longitude);
                $data->biaya_ke_lokasi = (int)$biayaPerKm * (int)$distance;
                # End calculate 
                $data->save();
                if (!$data) {
                    DB::rollback();
                    return Help::custom_response(500, "error", 'Pendaftaran homecare gagal', null);
                }
                foreach ($request->layanan_id as $key => $val) {
                    $data2 = new LayananPermintaanHc;
                    $data2->permintaan_id = $data->id_permintaan_hc;
                    $data2->layanan_id = $val;
                    $data2->save();
                    if (!$data) {
                        DB::rollback();
                        return Help::custom_response(500, "error", 'Error save layanan homecare', null);
                    }
                }
                DB::commit();
                return Help::custom_response(200, "success", "Ok.", Help::callbackRegistHc($data));
            } catch (\Throwable $e) {
                $log = ['ERROR PENDAFTARAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::custom_response(200, "error", $validate->errors()->all()[0], null);
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
            if (!$data) {
                return Help::custom_response(500, "error", 'Gagal menyelesaikan permintaan homecare', null);
            }
            return Help::custom_response(200, "success", 'Ok', $data);
        } catch (\Throwable $e) {
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
            if (!$data) {
                return Help::custom_response(500, "error", 'Gagal membatalkan permintaan homecare', null);
            } 
            return Help::custom_response(200, "success", 'Ok', $data);
        } catch (\Throwable $e) {
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
            $data = User::where('id', $request->id)->first();
            if ($data) {
                return Help::custom_response(200, "success", "found", $data);
            }
            return Help::custom_response(404, "error", "Data not found.", null);
        } catch (\Throwable $e) {
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
