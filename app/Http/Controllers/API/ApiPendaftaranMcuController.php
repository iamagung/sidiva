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
use App\Models\LayananPermintaanMcu;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class ApiPendaftaranMcuController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getLayananMcu($param) {
        $data = LayananMcu::where('kategori_layanan', strtoupper($param))->get();
        if (count($data) > 0) {
            return Help::custom_response(200, "success", "Berhasil", $data);
        }
        return Help::custom_response(404, "error", "Data not found.", null);
    }

    public function getDetailLayananMcu($id) {
        $data = LayananMcu::where('id_layanan', $id)->first();
        if ($data) {
            return Help::custom_response(200, "success", 'Success', $data);
        }
        return Help::custom_response(204, "error", 'Not found', null);
    }

    public function getSyaratAturan(Request $request) {
        $data = SyaratMcu::where('id_syarat_mcu', 1)->first();
        if ($data) {
            return Help::custom_response(200, "success", 'Success', $data);
        }
        return Help::custom_response(204, "error", 'Not found', null);
    }

    public function pesanJadwalMcu(Request $request) {
        $validate = Validator::make($request->all(),[
            'detail_pasien.*.nik' => 'required',
            'detail_pasien.*.nama' => 'required',
            'detail_pasien.*.tempat_lahir' => 'required',
            'detail_pasien.*.tanggal_lahir' => 'required',
            'detail_pasien.*.jenis_kelamin' => 'required',
            'detail_pasien.*.alamat' => 'required',
            'detail_pasien.*.telepon' => 'required',
            'detail_layanan.*.layanan_id' => 'required',
            'detail_pasien.*.tanggal_kunjungan' => 'required',
            'detail_pasien.*.waktu_layanan' => 'required'
        ],[
            'detail_pasien.*.nik.required' => 'NIK Wajib Diisi',
            'detail_pasien.*.nama.required' => 'Nama Lengkap Wajib Di isi',
            'detail_pasien.*.tempat_lahir.required' => 'Tempat Lahir Wajib Di isi',
            'detail_pasien.*.tanggal_lahir.required' => 'Tanggal Lahir Wajib Di isi',
            'detail_pasien.*.jenis_kelamin.required' => 'Jenis Kelamin Wajib Di isi',
            'detail_pasien.*.alamat.required' => 'Alamat Wajib Di isi',
            'detail_pasien.*.telepon.required' => 'Telepon Wajib Di isi',
            'detail_layanan.*.layanan_id.required' => 'Jenis Layanan Wajib Di isi',
            'detail_pasien.*.tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Diisi',
            'detail_pasien.*.waktu_layanan.required' => 'Waktu Layanan Wajib Diisi'
        ]);
        // return $request;
        if (!$validate->fails()) {
            DB::beginTransaction();
            try {
                $arrPermintaan = array();
                $arrLayananPermintaan = array();
                foreach ($request->detail_pasien as $key => $val) {
                    $tanggal = date('Y-m-d');
                    $tanggalKunjungan = date('Y-m-d', strtotime($val['tanggal_kunjungan']));
                    $timeCur = date('H:i');
                    $dayName = date('D', strtotime($tanggalKunjungan));
                    if($tanggalKunjungan<$tanggal){# Jika tanggal periksa kemarin{back date}
                        return Help::resApi('Tanggal sudah terlewat.',400);
                    }
                    # Menghitung selisih hari antara tanggal order dan tanggal pendaftaran
                    $selisih_hari = strtotime($tanggal) - strtotime($tanggalKunjungan);
                    $selisih_hari = $selisih_hari / (60 * 60 * 24); # Menghitung selisih hari
                    if ($selisih_hari > -1 || $selisih_hari < -3) { #Pengecekan pendaftaran hanya bisa (H-3) - (H-1)
                        return Help::resApi('Pendaftaran bisa dilakukan H-3 sampai H-1',400);
                    }
                    if ($check_nik = $this->checkByNik($val['nik'], $tanggalKunjungan) > 0) {# pengecekan agar tidak dobel
                        return Help::custom_response(500, "error", 'Nik telah digunakan untuk mendaftar pada tanggal '.Help::dateIndo($tanggalKunjungan), null);
                    }
                    $pengaturan = PengaturanMcu::where('id_pengaturan_mcu', 1)->first();
                    if ($dayName == 'Mon') { # Senin
                        if ($pengaturan->seninBuka == '' || $pengaturan->seninTutup == '') { # If Tidak ada jadwal
                            return Help::resApi('Tidak ada jadwal hari ini.',400);
                        } else {
                            if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->seninBuka) {
                                return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->seninBuka,400);
                            } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->seninTutup) {
                                return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->seninTutup,400);
                            }
                        }
                    }
                    if ($dayName == 'Tue') { # Selasa
                        if ($pengaturan->selasaBuka == '' || $pengaturan->selasaTutup == '') { # If Tidak ada jadwal
                            return Help::resApi('Tidak ada jadwal hari ini.',400);
                        } else {
                            if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->selasaBuka) {
                                return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->selasaBuka,400);
                            } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->selasaTutup) {
                                return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->selasaTutup,400);
                            }
                        }
                    }
                    if ($dayName == 'Wed') { # Rabu
                        if ($pengaturan->rabuBuka == '' || $pengaturan->rabuTutup == '') { # If Tidak ada jadwal
                            return Help::resApi('Tidak ada jadwal hari ini.',400);
                        } else {
                            if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->rabuBuka) {
                                return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->rabuBuka,400);
                            } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->rabuTutup) {
                                return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->rabuTutup,400);
                            }
                        }
                    }
                    if ($dayName == 'Thu') { # Kamis
                        if ($pengaturan->kamisBuka == '' || $pengaturan->kamisTutup == '') { # If Tidak ada jadwal
                            return Help::resApi('Tidak ada jadwal hari ini.',400);
                        } else {
                            if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->kamisBuka) {
                                return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->kamisBuka,400);
                            } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->kamisTutup) {
                                return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->kamisTutup,400);
                            }
                        }
                    }
                    if ($dayName == 'Fri') { # Jumat
                        if ($pengaturan->jumatBuka == '' || $pengaturan->jumatTutup == '') { # If Tidak ada jadwal
                            return Help::resApi('Tidak ada jadwal hari ini.',400);
                        } else {
                            if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->jumatBuka) {
                                return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->jumatBuka,400);
                            } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->jumatTutup) {
                                return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->jumatTutup,400);
                            }
                        }
                    }
                    if ($dayName == 'Sat') { # Sabtu
                        if ($pengaturan->sabtuBuka == '' || $pengaturan->sabtuTutup == '') { # If Tidak ada jadwal
                            return Help::resApi('Tidak ada jadwal hari ini.',400);
                        } else {
                            if ($tanggalKunjungan==$tanggal && $timeCur<$pengaturan->sabtuBuka) {
                                return Help::resApi('Pendaftaran bisa dilakukan mulai jam '.$pengaturan->sabtuBuka,400);
                            } else if($tanggalKunjungan==$tanggal && $timeCur>$pengaturan->sabtuTutup) {
                                return Help::resApi('Pendaftaran sudah ditutup pada jam '.$pengaturan->sabtuTutup,400);
                            }
                        }
                    }
                    if($dayName == 'Sun'){ # If tanggal periksa adalah hari minggu
                        return Help::resApi('Tidak bisa mengambil antrian pada hari minggu.',400);
                    }
                    if (strlen($val['nik'])!=16) {
                        return Help::resApi('NIK tidak sesuai standar 16 digit.',400);
                    }
                    # insert to permintaan mcu
                    $newPermintaan = PermintaanMCU::create([
                        'nik'  => $val['nik'],
                        'nama'  => $val['nama'],
                        'alamat'  => $val['alamat'],
                        'tempat_lahir'  => $val['tempat_lahir'],
                        'tanggal_lahir'  => $val['tanggal_lahir'],
                        'tanggal_order'  => date('Y-m-d'),
                        'tanggal_kunjungan'  => $val['tanggal_kunjungan'],
                        'jenis_kelamin'  => $val['jenis_kelamin'],
                        'telepon'  => $val['telepon'],
                        'status_pembayaran'  => "pending",
                        'status_pasien'  => "belum",
                        'jenis_mcu'  => $request->jenis_mcu
                    ]);
                    if(!$newPermintaan->save()) { #jika gagal simpan permintaan
                        DB::rollback();
                        return Help::resApi('gagal simpan permintaan', 400);
                    }
                    foreach ($request->detail_layanan as $key => $v) {
                        $layanan = new LayananPermintaanMcu;
                        $layanan->layanan_id = $v['layanan_id'];
                        $layanan->permintaan_id = $newPermintaan->id_permintaan;
                        if(!$layanan->save()){ #jika gagal simpan pelayanan
                            DB::rollback();
                            return Help::resApi('gagal simpan layanan', 400);        
                        }
                    }
                }
                DB::commit();
                return Help::custom_response(200, "success", 'Success', null);
            } catch (\Throwable $e) {
                $log = ['ERROR PENDAFTARAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::custom_response(400, "error", $validate->errors()->all()[0], null);
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
