<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TmCustomer;
use App\Models\LayananMcu;
use App\Models\SyaratMcu;
use App\Models\TransaksiMCU;
use App\Models\PermintaanMcu;
use App\Models\PengaturanMcu;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class ApiPendaftaranMcuController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getLayananMcu($param)
    {
        $data = LayananMcu::where('kategori_layanan', strtoupper($param))->get();
        if (count($data) > 0) {
            return Help::custom_response(200, "success", "Berhasil", $data);
        }
        return Help::custom_response(404, "error", "Data not found.", null);
    }

    public function getDetailLayananMcu($id)
    {
        $data = LayananMcu::where('id_layanan', $id)->first();
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
        return response()->json($respon);
    }

    public function getSyaratAturan(Request $request)
    {
        $data = SyaratMcu::where('id_syarat_mcu', 1)->first();
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
    }

    public function pesanJadwalMcu(Request $request)
    {
        if ($request->no_rm == "") {
            $validate = Validator::make($request->all(),[
                'nik' => 'required',
                'nama' => 'required',
                'tanggal_lahir' => 'required',
                'alamat' => 'required',
                'jenis_pembayaran' => 'required',
                'layanan_id' => 'required',
                'tanggal_kunjungan' => 'required',
                'jenis_kelamin' => 'required',
                'no_telepon' => 'required'
            ],[
                'nik.required' => 'Nik Wajib Di isi',
                'nama.required' => 'Nama Wajib Di isi',
                'tanggal_lahir.required' => 'Tanggal Lahir Wajib Di isi',
                'alamat.required' => 'Alamat Wajib Di isi',
                'jenis_pembayaran.required' => 'Jenis Pembayaran  Wajib Di isi',
                'layanan_id.required' => 'Jenis Layanan  Wajib Di isi',
                'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Di isi',
                'jenis_kelamin.required' => 'Jenis Kelamin Wajib Di isi',
                'no_telepon.required' => 'No. telepon Wajib Di isi'
            ]);
        }else{
            $validate = Validator::make($request->all(),[
                'jenis_pembayaran' => 'required',
                'layanan_id' => 'required',
                'tanggal_kunjungan' => 'required'
            ],[
                'jenis_pembayaran.required' => 'Jenis Pembayaran Wajib Di isi',
                'layanan_id.required' => 'Jenis Layanan  Wajib Di isi',
                'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Di isi',
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
            $pengaturan = PengaturanMcu::where('id_pengaturan_mcu', 1)->first();
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
            // if ($dayName == 'Fri') { # Jum'at
            //     if ($pengaturan->jumatBuka == '' || $pengaturan->jumatTutup == '') { # If Tidak ada jadwal
            //         return Help::resApi('Tidak ada jadwal hari ini.',201);
            //     } else {
            //         if ($request->tanggal_kunjungan==$dateCur && $timeCur<$pengaturan->jumatBuka) {
            //             return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->jumatBuka,201);
            //         } else if($request->tanggal_kunjungan==$dateCur && $timeCur>$pengaturan->jumatTutup) {
            //             return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->jumatTutup,201);
            //         }
            //     }
            // }
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
                $noreg = Help::generateNoRegMcu($request);
                $strRandom = Help::randomString(7);
                $data = new PermintaanMcu;
                $data->no_rm                = !empty($pasien) ? $request->no_rm : null;
                $data->no_registrasi        = $noreg;
                $data->kode_booking         = $strRandom;
                $data->nik                  = !empty($pasien) ? $pasien->NoKtp : $request->nik;
                $data->nama                 = !empty($pasien) ? $pasien->NamaCust : strtoupper($request->nama);
                $data->alamat               = !empty($pasien) ? $pasien->Alamat : $request->alamat;
                // $data->layanan_id           = $request->layanan_id;
                $data->layanan_id           = implode($request->layanan_id,',');
                $data->tanggal_order        = date('Y-m-d');
                $data->tanggal_kunjungan    = $request->tanggal_kunjungan;
                $data->jenis_pembayaran     = $request->jenis_pembayaran;
                $data->no_bpjs              = !empty($request->no_bpjs) ? $request->no_bpjs : null;
                $data->no_rujukan           = !empty($request->no_rujukan) ? $request->no_rujukan : null;
                $data->tanggal_lahir        = !empty($pasien) ? $pasien->TglLahir : $request->tanggal_lahir;
                $data->jenis_kelamin        = !empty($pasien) ? $pasien->JenisKel : $request->jenis_kelamin;
                $data->telepon              = !empty($pasien) ? $pasien->Telp : $request->no_telepon;
                $data->status_pasien        = 'belum';
                $data->save();

                if ($data) {
                    return response()->json([
                        'metadata' => [
                            'message' => 'Success',
                            'code'    => 200,
                        ],
                        'response' => Help::callbackRegistMcu($data),
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

    public function getListPembayaranMcu($id)
    {
        try{
            $permintaan = PermintaanMcu::where('id_permintaan', $id)->first();
            $id_layanan = explode(",", $permintaan->layanan_id);
            $layanan = DB::table('layanan_mcu')->whereIn('id_layanan', $id_layanan)->get();

            if ($permintaan) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $layanan
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
            $log = ['ERROR GET LIST PEMBAYARAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getInvoiceMcu($id)
    {
        try{
            $permintaan = PermintaanMcu::where('id_permintaan', $id)->first();
            $pasien = DB::connection('dbrsud')->table('tm_customer')->where('NoKtp', $permintaan->nik)->first();
            $id_layanan = explode(",", $permintaan->layanan_id);
            $layanan = DB::table('layanan_mcu')->whereIn('id_layanan', $id_layanan)->get();
            $sum = 0;
            // return $layanan;
            for($i = 0; $i < count($layanan); $i++){
                $sum += $layanan[$i]->harga;
            }
            # update to permintaan_mcu
            $permintaan->biaya = $sum;
            $permintaan->metode_pembayaran = 'Tunai';
            $permintaan->save();
            # insert to transaksi_mcu
            $transaksi = new TransaksiMCU;
            $transaksi->id_permintaan_mcu = $id;
            $transaksi->nominal           = $sum;
            $transaksi->invoice           = "INV/".date('Ymd')."/MCU"."/".rand(20, 200);
            $transaksi->status            = 'pending';
            $transaksi->save();

            $data = [
                'permintaan'    => $permintaan,
                'pasien'        => $pasien,
                'layanan'       => $layanan,
                'transaksi'     => $transaksi
            ];
            if ($permintaan) {
                $return = [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $data
                ];
            } else {
                $return = [
                    'metaData' => [
                        "code" => 201,
                        "message" => 'Gagal.'
                    ],
                    'response' => []
                ];
            }
            return view('admin.invoice.mcu', $data);

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET LIST PEMBAYARAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function selesaikanPermintaanMCU(Request $request)
    {
        try{
            $permintaan = PermintaanMcu::where('id_permintaan', $request->id_permintaan)->first();
            $permintaan->status_pasien = 'selesai';
            $permintaan->save();

            if ($permintaan) {
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
                        "message" => 'Gagal.'
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

    public function riwayatPermintaanMCU($params)
    {
        try{
            $permintaan = PermintaanMcu::where('no_rm', $params)
                ->where('status_pasien', 'selesai')
                ->orderBy('id_permintaan', 'DESC')
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

    public function batalPermintaanMCU(Request $request)
    {
        try{
            $permintaan = PermintaanMcu::where('id_permintaan', $request->id_permintaan)->first();
            $permintaan->status_pasien = 'batal';
            $permintaan->save();

            if ($permintaan) {
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
                        "message" => 'Gagal.'
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

    public function saveRatingMCU(Request $request)
    {
        try{
            $data = [
                'permintaan_mcu_id' => $request->id_permintaan_mcu,
                'comments'          => $request->comments,
                'star_rating'       => $request->rating
            ];
            $rating = DB::table('rating_mcu')->insert($data);

            if ($rating) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => DB::table('rating_mcu')->where('permintaan_mcu_id', $request->id_permintaan_mcu)->first()
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
            $log = ['ERROR SAVE RATING MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function checkByNik($nik, $tanggal) //check nik apakah sudah digunakan
    {
        $check = PermintaanMcu::where('nik','=',$nik)->where('tanggal_kunjungan','=',$tanggal)->count();
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
