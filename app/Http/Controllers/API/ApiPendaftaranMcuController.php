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
use App\Models\Rating;
use App\Models\PaymentPermintaan;
use App\Models\Activity;
use App\Helpers\Helpers as Help;
use App\Helpers\XenditHelpers;
use Validator, DB, Auth, Hash, DateTime;

class ApiPendaftaranMcuController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Jakarta');
    }
    public function getLayananMcu($kategori,$jenis) {
        $data = LayananMcu::where('kategori_layanan', strtoupper($kategori))->where('jenis_layanan',$jenis)->get();
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
            'jenis_mcu' => 'required',
            'tanggal_kunjungan' => 'required',
            'waktu_layanan' => 'required',
            'tempat_mcu' => 'required',
            'detail_pasien.*.nik' => 'required',
            'detail_pasien.*.nama' => 'required',
            'detail_pasien.*.tempat_lahir' => 'required',
            'detail_pasien.*.tanggal_lahir' => 'required',
            'detail_pasien.*.jenis_kelamin' => 'required',
            'detail_pasien.*.alamat' => 'required',
            'detail_pasien.*.telepon' => 'required',
            'detail_layanan.*.layanan_id' => 'required'
        ],[
            'jenis_mcu.required' => 'Jenis MCU wajib diisi',
            'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Diisi',
            'waktu_layanan.required' => 'Waktu Layanan Wajib Diisi',
            'tempat_mcu.required' => 'Tempat MCU Wajib Diisi',
            'detail_pasien.*.nik.required' => 'NIK Wajib Diisi',
            'detail_pasien.*.nama.required' => 'Nama Lengkap Wajib Di isi',
            'detail_pasien.*.tempat_lahir.required' => 'Tempat Lahir Wajib Di isi',
            'detail_pasien.*.tanggal_lahir.required' => 'Tanggal Lahir Wajib Di isi',
            'detail_pasien.*.jenis_kelamin.required' => 'Jenis Kelamin Wajib Di isi',
            'detail_pasien.*.alamat.required' => 'Alamat Wajib Di isi',
            'detail_pasien.*.telepon.required' => 'Telepon Wajib Di isi',
            'detail_layanan.*.layanan_id.required' => 'Jenis Layanan Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::custom_response(400, "error", $validate->errors()->all()[0], null);
        }
        DB::beginTransaction();
        try {
            foreach ($request->detail_pasien as $key => $val) {
                $tanggal = date('Y-m-d');
                $tanggalKunjungan = date('Y-m-d', strtotime($request->tanggal_kunjungan));
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
                    return Help::custom_response(400, "error", 'Nik telah digunakan untuk mendaftar pada tanggal '.Help::dateIndo($tanggalKunjungan), null);
                }
                $pengaturan = PengaturanMcu::where('id_pengaturan_mcu', 1)->first();
                if (!$pengaturan) {
                    return Help::custom_response(400, "error", 'Pengaturan homecare belum diatur, silahkan menghubungi admin.', null);
                }
                if (ceil($request->jarak_ke_lokasi) > $pengaturan->jarak_maksimal) { #lokasi pasien tidak boleh lebih dari maksimal pengaturan jarak
                    return Help::resApi('Pendataran tidak bisa dilakukan, Lokasi diluar jangkauan pelayanan',400);
                }
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
                # insert permintaan mcu
                $newPermintaan = PermintaanMCU::create([
                    'nik'  => $val['nik'],
                    'nama'  => strtoupper($val['nama']),
                    'alamat'  => $val['alamat'],
                    'tempat_lahir'  => strtoupper($val['tempat_lahir']),
                    'tanggal_lahir'  => $val['tanggal_lahir'],
                    'tanggal_order'  => date('Y-m-d'),
                    'tanggal_kunjungan'  => $tanggalKunjungan,
                    'jenis_kelamin'  => $val['jenis_kelamin'],
                    'telepon'  => $val['telepon'],
                    'status_pembayaran'  => "pending",
                    'status_pasien'  => "belum",
                    'jenis_mcu'  => $request->jenis_mcu,
                    'tempat_mcu' => $request->tempat_mcu,
                    'jarak_ke_lokasi' => $request->tempat_mcu=='RS'?null:$request->jarak_ke_lokasi,
                    'biaya_ke_lokasi' => $request->tempat_mcu=='RS'?0:(int)$pengaturan->biaya_per_km * ceil($request->jarak_ke_lokasi)
                ]);
                if(!$newPermintaan->save()) { #jika gagal simpan permintaan mcu
                    DB::rollback();
                    return Help::resApi('gagal simpan permintaan', 400);
                }
                foreach ($request->detail_layanan as $key => $v) {
                    $getLayanan = LayananMcu::where('id_layanan', $v['layanan_id'])->first();
                    $getLayananPermintaan = LayananPermintaanMcu::select(
                        'layanan_permintaan_mcu.*',
                        'pm.id_permintaan',
                        'pm.tanggal_kunjungan',
                        'pm.status_pembayaran',
                        'pm.status_pasien'
                    )
                    ->leftJoin('permintaan_mcu as pm','pm.id_permintaan','layanan_permintaan_mcu.permintaan_id')
                    ->where('layanan_permintaan_mcu.layanan_id', $v['layanan_id'])
                    ->where('pm.tanggal_kunjungan', $request->tanggal_kunjungan)
                    ->count();
                    if ($getLayananPermintaan>$getLayanan->kuota_layanan) {#Pengecekan jika kuota layanan sudah penuh
                        DB::rollback();
                        return Help::resApi('Kuota layanan habis silahkan reschedule jadwal',400);
                    }
                    if ($request->jenis_mcu=='kelompok') {# Jika mcu kelompok
                        if (count($request->detail_pasien)>$getLayanan->maksimal_peserta) {#Pengecekan maksimal peserta untuk mcu kelompok
                            DB::rollback();
                            return Help::resApi('Maksimal peserta '.$getLayanan->maksimal_peserta.' orang untuk layanan '.$getLayanan->nama_layanan,400);
                        }
                    }
                    # Insert layanan permintaan mcu
                    $layanan = new LayananPermintaanMcu;
                    $layanan->layanan_id = $v['layanan_id'];
                    $layanan->permintaan_id = $newPermintaan->id_permintaan;
                    if(!$layanan->save()){ #jika gagal simpan pelayanan
                        DB::rollback();
                        return Help::resApi('gagal simpan layanan', 400);
                    }
                }
                # Insert payment
                $payment = new PaymentPermintaan;
                $payment->permintaan_id = $newPermintaan->id_permintaan;
                $payment->nominal = Help::dataRegistrasiMCU($newPermintaan->id_permintaan)['subtotal'];
                $payment->jenis_layanan = 'mcu';
                $payment->tgl_expired = date('Y-m-d H:i:s', strtotime('+30 minute'));
                $payment->status = 'UNCONFIRMED';
                if(!$payment->save()){
                    DB::rollback();
                    return Help::custom_response(400, "error", "Gagal simpan Payment permintaan", null);
                }
            }
            $saveLog = Activity::store(Auth::user()->id, "Booking medical check up");
            if(!$saveLog){
                DB::rollback();
                return Help::custom_response(400, "error", "Gagal simpan log activity", null);
            }
            DB::commit();
            return Help::custom_response(200, "success", 'Success', Help::dataRegistrasiMCU($newPermintaan->id_permintaan));
        } catch (\Throwable $e) {
            DB::rollback();
            $log = ['ERROR PENDAFTARAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getListPermintaanMCU($id) {
        try{
            return Help::dataRegistrasiMCU($id);
        } catch (\Throwable $e) {
            $log = ['ERROR GET LIST PERMINTAAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getListMcu() {
        // Auth::user()->id;
    }

    public function invoiceMCU(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan' => 'required'
        ],[
            'id_permintaan.required' => 'ID Permintaan MCU Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::custom_response(400, "error", $validate->errors()->all()[0], null);
        }
        try{
            # Cari data permintaan berdasarkan id_permintaan_telemedicine
            if(!$permintaan = PermintaanMcu::where('id_permintaan', $request->id_permintaan)->first()) {
                return Help::custom_response(400, "error", "Permintaan mcu tidak ditemukan", null);
            }
            if($permintaan->status_pasien=='belum') {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Permintaan Belum Disetujui Petugas'
                    ],
                    'response' => []
                ];
            }
            # Cari data payment berdasarkan permintaan_id dan jenis layanan telemedicine
            if (!$payment = PaymentPermintaan::where('permintaan_id', $request->id_permintaan)->where('jenis_layanan', 'mcu')->first()) {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Permintaan Belum Disetujui Petugas'
                    ],
                    'response' => []
                ];
            }
            # Jika sudah memiliki invoice_id dari xendit, maka di carikan dengan getInvoice
            if (!$payment->invoice_id == "") {
                # Cek apakah ditolak oleh petugas
                if(in_array($permintaan->status_pasien,['tolak','batal'])) {
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
            $layananMcu = LayananPermintaanMcu::select(
                    'layanan_permintaan_mcu.id_layanan_permintaan_mcu',
                    'layanan_permintaan_mcu.permintaan_id',
                    'layanan_permintaan_mcu.layanan_id',
                    'lc.id_layanan',
                    'lc.nama_layanan',
                    'lc.harga'
                )
                ->leftJoin('layanan_mcu as lc','lc.id_layanan','layanan_permintaan_mcu.layanan_id')
                ->where('layanan_permintaan_mcu.permintaan_id', $request->id_permintaan)->get();
            if(count($layananMcu)==0){
                DB::rollback();
                return Help::resApi('Tidak ada layanan yang perlu dibayar',204);
            }
            $items = array();
            foreach ($layananMcu as $key => $val) {
                $newItem = (object)[
                    'name' => $val->nama_layanan,
                    'price' => (float)$val->harga,
                    'quantity' => 1
                ];
                $items[] = $newItem;
            }
            if(!empty($permintaan->tempat_mcu!='RS')){
                $items[] = (object)[
                    'name' => 'Ongkos Kirim',
                    'price' => (float)$permintaan->biaya_ke_lokasi,
                    'quantity' => 1
                ];
            }
            $new_invoice = XenditHelpers::createInvoice((string)$payment->id_payment_permintaan, 'Pembayaran Permintaan Layanan MCU', $payment->nominal, (string)$date_diff, $items)->getData();
            if(!$new_invoice->metaData->code == 200) {
                return Help::resApi('Terjadi kesalahan sistem',500);
            }

            # Update Invoice ID di payment permintaan
            $payment->invoice_id = $new_invoice->response->id;
            $payment->status = $new_invoice->response->status;
            $payment->save();

            return [
                'metaData' => [
                    "code" => 200,
                    "message" => 'Pembayaran berhasil dibuat'
                ],
                'response' => $new_invoice->response->invoice_url
            ];

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR INVOICE HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function batalOtomatisPermintaanMCU(Request $request) {
        try {
            $data = PermintaanMcu::where('id_permintaan', $request->id_permintaan)->first();
            $data->status_pasien = 'batal';
            $data->save();
            if (!$data) {
                return Help::custom_response(400, "error", 'Gagal membatalkan permintaan mcu', null);
            }
            return Help::custom_response(200, "success", 'Ok', $data);
        } catch (\Throwable $e) {
            $log = ['ERROR BATAL LAYANAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function selesaikanPermintaanMCU(Request $request) {
        try{
            $data = PermintaanMcu::where('id_permintaan', $request->id_permintaan)->first();
            $data->status_pasien = 'selesai';
            $data->save();
            if (!$data) {
                return Help::custom_response(400, "error", 'Gagal selesaikan permintaan mcu', null);
            }
            return Help::custom_response(200, "success", 'Ok', $data);
        } catch (\Throwable $e) {
            $log = ['ERROR SELESIKAN LAYANAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function riwayatPermintaanMCU(Request $request) {
        try{
            $data = PermintaanMcu::where('nik', $request->nik)
                ->whereIn('status_pasien', ['proses','selesai'])
                ->orderBy('id_permintaan', 'DESC')
                ->get();
            if (count($permintaan) > 0) {
                return Help::custom_response(200, "success", 'Ok', $data);
            }
            return Help::custom_response(204, "success", 'Ok', null);
        } catch (\Throwable $e) {
            $log = ['ERROR RIWAYAT PERMINTAAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
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

    public function saveRatingMCU(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan' => 'required',
            'star_rating' => 'required',
            'comments' => 'required',
        ],[
            'id_permintaan.required' => 'ID Permintaan Homecare Wajib Di isi',
            'star_rating.required' => 'Bintang Wajib Di isi',
            'comments.required' => 'Komentar Wajib Di isi',
        ]);
        if ($validate->fails()) {
            return Help::custom_response(400, "error", $validate->errors()->all()[0], null);
        }
        try{
            if(!$permintaan = PermintaanMcu::where('id_permintaan', $request->id_permintaan)->first()){
                return Help::custom_response(204, "error", 'Permintaan mcu tidak ditemukan', null);
            }
            if(!$rating = Rating::where('permintaan_id', $request->id_permintaan)->where('jenis_layanan', 'mcu')->first()){
                $rating = new Rating;
                $rating->jenis_layanan = 'mcu';
                $rating->permintaan_id = $request->id_permintaan;
            }
            $rating->star_rating = $request->star_rating;
            $rating->comments = $request->comments;
            if ($rating->save()) {
                return Help::custom_response(200, "success", 'Ok', $rating);
            }
            return Help::custom_response(400, "error", 'gagal', null);
        } catch (\Throwable $e) {
            $log = ['ERROR SAVE RATING MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    // public addLayananTambahanMCU() {

    // }

    public function checkByNik($nik, $tanggal) { //check nik apakah sudah digunakan{
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
