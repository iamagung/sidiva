<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TmCustomer;
use App\Models\LayananHC;
use App\Models\PaketHC;
use App\Models\Activity;
use App\Models\PermintaanHC;
use App\Models\PengaturanHC;
use App\Models\SyaratHC;
use App\Models\TenagaMedis;
use App\Models\TransaksiHC;
use App\Models\User;
use App\Models\LayananPermintaanHc;
use App\Models\Rating;
use App\Models\PaymentPermintaan;
use App\Models\ResepObat;
use App\Models\ResepObatDetail;
use App\Helpers\Helpers as Help;
use App\Helpers\XenditHelpers;
use Validator, DB, Auth, Hash, DateTime;

class ApiPendaftaranHcController extends Controller
{
    public function __construct() {
        date_default_timezone_set('Asia/Jakarta');
    }
    public function getLayananHC(Request $request) {
        try {
            $data = LayananHC::all();
            if (count($data) > 0) {
                return Help::custom_response(200, "success", "Ok", $data);
            }
            return Help::custom_response(204, "error", "data tidak ditemukan", null);
        } catch (\Throwable $e) {
            $log = ['ERROR GET LAYANAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function getDetailLayananHC(Request $request) {
        try {
            if(!$request->id_layanan_hc){ # wajib send param id layanan
                return Help::custom_response(400, "error", "id layanan wajib diisi", null);
            }
            $data = LayananHC::where('id_layanan_hc', $request->id_layanan_hc)->first();
            if(!$data){ #Jika tidak ada data
                return Help::custom_response(204, "error", "data tidak ditemukan", null);
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
                return Help::custom_response(204, "error", "data tidak ditemukan", null);
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
            'layanan_id.*' => 'required',
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
            'layanan_id.*.required' => 'Jenis Layanan Wajib Di isi',
            'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Diisi',
            'waktu_layanan.required' => 'Waktu Layanan Wajib Diisi'
        ]);
        if (!$validate->fails()) {
            $timeCur = date('H:i');
            $tanggal = date('Y-m-d');
            $tanggalKunjungan = date('Y-m-d', strtotime($request->tanggal_kunjungan));
			$day = date('D', strtotime($tanggalKunjungan));
            if($tanggalKunjungan<$tanggal){# Jika tanggal periksa kemarin{back date}
                return Help::resApi('Tanggal sudah terlewat.',400);
            }
            # Menghitung selisih hari antara tanggal order dan tanggal pendaftaran
            $selisih_hari = strtotime($tanggal) - strtotime($tanggalKunjungan);
            $selisih_hari = $selisih_hari / (60 * 60 * 24); # Menghitung selisih hari
            if ($selisih_hari > -1 || $selisih_hari < -3) { #Pengecekan pendaftaran hanya bisa (H-3) - (H-1)
                return Help::resApi('Pendaftaran bisa dilakukan H-3 sampai H-1',400);
            }
            if ($check_nik = $this->checkByNik($request->nik, $tanggalKunjungan) > 0) {# pengecekan agar tidak dobel
                return Help::custom_response(400, "error", 'Nik telah digunakan untuk mendaftar pada tanggal '.Help::dateIndo($tanggalKunjungan), null);
            }
            $pengaturan = PengaturanHC::where('id_pengaturan_hc', 1)->first();
            if (!$pengaturan) {
                DB::rollback();
                return Help::custom_response(400, "error", 'Pengaturan homecare belum diatur, silahkan menghubungi admin.', null);
            }
            if (ceil($request->jarak_ke_lokasi) > $pengaturan->jarak_maksimal) { #lokasi pasien tidak boleh lebih dari maksimal pengaturan jarak
                return Help::resApi('Pendataran tidak bisa dilakukan, Lokasi diluar jangkauan pelayanan',400);
            }
            if ($day == 'Mon') { # Senin
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
            if ($day == 'Tue') { # Selasa
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
            if ($day == 'Wed') { # Rabu
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
            if ($day == 'Thu') { # Kamis
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
            if ($day == 'Fri') { # Jum'at
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
            if ($day == 'Sat') { # Sabtu
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
			if($day == 'Sun'){ # If tanggal periksa adalah hari minggu
				return Help::resApi('Tidak bisa mengambil antrian pada hari minggu.',201);
			}
            if (strlen($request->nik)!=16) { #Validasi length nik
                return Help::resApi('NIK tidak sesuai standar 16 digit.',400);
            }
            $pasien = TmCustomer::where('NoKtp','=',$request->nik)->first();
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
                $data->keterangan_lokasi = $request->keterangan_lokasi;
                $data->latitude          = $request->latitude;
                $data->longitude         = $request->longitude;
                $data->jenis_pembayaran  = $request->jenis_pembayaran;
                $data->no_telepon        = !empty($pasien) ? $pasien->Telp : $request->telepon;
                $data->jenis_kelamin     = !empty($pasien) ? $pasien->JenisKel : $request->jenis_kelamin;
                $data->waktu_layanan     = $request->waktu_layanan;
                $data->status_pasien     = 'belum';
                $data->status_pembayaran = 'pending';
                $data->jarak_ke_lokasi   = $request->jarak_ke_lokasi;
                $data->biaya_ke_lokasi = (int)$pengaturan->biaya_per_km * ceil($request->jarak_ke_lokasi);
                $data->save();
                if (!$data) {
                    DB::rollback();
                    return Help::custom_response(400, "error", 'Pendaftaran homecare gagal', null);
                }
                # Insert layanan permintaan homecare
                foreach ($request->layanan_id as $key => $val) {
                    $data2 = new LayananPermintaanHc;
                    $data2->permintaan_id = $data->id_permintaan_hc;
                    $data2->layanan_id = $val;
                    $data2->save();
                    if (!$data) {
                        DB::rollback();
                        return Help::custom_response(400, "error", 'Error save layanan homecare', null);
                    }
                }
                # Insert payment
                $payment = new PaymentPermintaan;
                $payment->permintaan_id = $data->id_permintaan_hc;
                $payment->nominal = Help::dataRegistrasiHomecare($data->id_permintaan_hc)['subtotal'];
                $payment->jenis_layanan = 'homecare';
                $payment->tgl_expired = date('Y-m-d H:i:s', strtotime('+30 minute'));
                $payment->status = 'UNCONFIRMED';
                if(!$payment->save()){
                    DB::rollback();
                    return Help::custom_response(400, "error", "Gagal simpan Payment permintaan", null);
                }
                # Log activity
                $activity = Activity::store(Auth::user()->id,"Booking homecare");
                if (!$activity) {
                    DB::rollback();
                    return Help::custom_response(400, "error", "Gagal simpan activity", null);
                }
                DB::commit();
                return Help::custom_response(200, "success", "Ok", Help::dataRegistrasiHomecare($data->id_permintaan_hc));
            } catch (\Throwable $e) {
                $log = ['ERROR PENDAFTARAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }else{
            return Help::custom_response(400, "error", $validate->errors()->all()[0], null);
        }
    }
    public function addLayananHomecare(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_hc' => 'required',
            'layanan_id.*' => 'required',
            'alasan_penambahan' => 'required'
        ],[
            'id_permintaan_hc.required' => 'ID Permintaan Homecare Wajib Di isi',
            'layanan_id.*.required' => 'Layanan Wajib Di isi',
            'alasan_penambahan.required' => 'Alasan Penambahan Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::custom_response(400, "error", $validate->errors()->all()[0], null);
        }
        DB::beginTransaction();
        try {
            foreach ($request->layanan_id as $layanan) {
                # Insert layanan permintaan homecare
                $data = new LayananPermintaanHC;
                $data->permintaan_id = $request->id_permintaan_hc;
                $data->layanan_id = $layanan;
                $data->alasan_penambahan = $request->alasan_penambahan;
                $data->is_confirm_pasien = true; // sementara default true
                $data->is_tambahan = true;
                if ($request->file) {
                    $fileName = $request->file->getClientOriginalName();
                    $filePath = 'layanan/' . $fileName;
                    $path = Storage::disk('public')->put($filePath, file_get_contents($request->file));
                    $path = Storage::disk('public')->url($path);
                    $data->file = $fileName;
                }
                $data->save();
                if (!$data) {
                    DB::rollback();
                    return Help::custom_response(204, "error", 'Gagal tambah layanan', null);
                }
            }
            # Insert payment
            $price = 0;
            $getLayanan = LayananPermintaanHc::select(
                    'layanan_permintaan_hc.*',
                    'l.id_layanan_hc',
                    'l.harga'
                )->leftJoin('layanan_hc as l', 'l.id_layanan_hc', 'layanan_permintaan_hc.layanan_id')
                ->where('is_tambahan', true)
                ->where('is_confirm_pasien', true)
                ->whereIn('layanan_id',$request->layanan_id)
                ->get();
            foreach ($getLayanan as $g) {
                $price += $g->harga;
            }
            $payment = new PaymentPermintaan;
            $payment->permintaan_id = $request->id_permintaan_hc;
            $payment->nominal = $price;
            $payment->jenis_layanan = 'homecare';
            $payment->tgl_expired = date('Y-m-d H:i:s', strtotime('+30 minute'));
            $payment->status = 'UNCONFIRMED';
            if(!$payment->save()){
                DB::rollback();
                return Help::custom_response(400, "error", "Gagal simpan Payment layanan", null);
            }
            # Log activity
            $activity = Activity::store(Auth::user()->id,"Tambah layanan homecare");
            if (!$activity) {
                DB::rollback();
                return Help::custom_response(400, "error", "Gagal simpan activity", null);
            }
            DB::commit();
            return Help::custom_response(200, "success", 'OK', null);
        } catch (\Throwable $e) {
            DB::rollback();
            $log = ['ERROR SELESAIKAN PELAYANAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function getListPermintaanHC(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_user' => 'required'
        ],[
            'id_user.required' => 'ID User Wajib Di isi'
        ]);
        if (!$validate->fails()) {
            try{
                $price = 0;
                $arrLayanan = [];
                $user = User::select('id','name','nik','no_rm')
                    ->leftJoin('users_android as ua','ua.user_id','users.id')
                    ->where('users.id',$request->id_user)
                    ->first();
                $permintaan = PermintaanHC::where('nik', $user->nik)
                ->whereIn('status_pasien', ['menunggu', 'belum', 'proses'])
                ->where('status_pembayaran', '=', 'pending')
                ->where('tanggal_kunjungan', '>', date('Y-m-d'))
                ->with('layanan_permintaan_hc',function($qq) {
                    $qq->with('layanan_hc');
                })
                ->orderBy('id_permintaan_hc','ASC')->get();
                foreach($permintaan as $k => $v){
                    $subtotal = 0;
                    foreach ($v->layanan_permintaan_hc as $l) {
                        $subtotal += $l->layanan_hc->harga;
                    }
                    $permintaan[$k]['subtotal'] = $subtotal+$v->biaya_ke_lokasi;
                    // return $v->layanan_permintaan_hc[0]->layanan_hc->harga;
                }
                if (count($permintaan)==0) {
                    return Help::custom_response(204, "error", "Data not found",null);
                }
                return Help::custom_response(200, "success", "Berhasil", $permintaan);
            } catch (\Throwable $e) {
                $log = ['ERROR GET LIST PEMBAYARAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        } else {
            return Help::resApi($validate->errors()->all()[0],400);
        }
    }
    public function invoiceHC(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_hc' => 'required'
        ],[
            'id_permintaan_hc.required' => 'ID Permintaan Homecare Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::custom_response(400, "error", $validate->errors()->all()[0], null);
        }
        try{
            # Cari data permintaan berdasarkan id_permintaan_telemedicine
            if(!$permintaan = PermintaanHC::where('id_permintaan_hc', $request->id_permintaan_hc)->first()) {
                return Help::custom_response(400, "error", "Permintaan homecare tidak ditemukan", null);
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
            if (!$payment = PaymentPermintaan::where('permintaan_id', $request->id_permintaan_hc)->where('jenis_layanan', 'homecare')->whereNotIn('status', ['EXPIRED','PAID','SETTLED'])->first()) {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Invoice tidak ditemukan'
                    ],
                    'response' => []
                ];
            }
            # Log activity
            $activity = Activity::store(Auth::user()->id,"Create invoice layanan homecare");
            if (!$activity) {
                DB::rollback();
                return Help::custom_response(400, "error", "Gagal simpan activity invoice homecare", null);
            }
            # Jika sudah memiliki invoice_id dari xendit, maka di carikan dengan getInvoice
            if (!$payment->invoice_id == "") {
                # Cek apakah ditolak oleh petugas
                if(in_array($permintaan->status_pasien,['tolak','batal'])) {
                    $payment->status = 'EXPIRED';
                    $invoice = XenditHelpers::expiredInvoice($payment->invoice_id)->getData();
                }
                // return $payment;
                $invoice = XenditHelpers::getInvoice($payment->invoice_id)->getData();
                if ($invoice->metaData->code == 200) {
                    $payment->status = $invoice->response->status;


                    $payment->save();
                    // return $invoice->response;
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
            $layananHomecare = LayananPermintaanHc::select(
                    'layanan_permintaan_hc.id_layanan_permintaan_hc',
                    'layanan_permintaan_hc.permintaan_id',
                    'layanan_permintaan_hc.layanan_id',
                    'layanan_permintaan_hc.status_bayar',
                    'lc.id_layanan_hc',
                    'lc.nama_layanan',
                    'lc.harga',
                    'lc.jumlah_hari'
                )
                ->leftJoin('layanan_hc as lc','lc.id_layanan_hc','layanan_permintaan_hc.layanan_id')
                ->where('layanan_permintaan_hc.permintaan_id','=',$request->id_permintaan_hc)
                ->where('layanan_permintaan_hc.status_bayar',null)
                ->get();
            if(count($layananHomecare)==0){
                DB::rollback();
                return Help::resApi('Tidak ada layanan yang perlu dibayar',204);
            }
            $items = array();
            foreach ($layananHomecare as $key => $val) {
                $newItem = (object)[
                    'name' => $val->nama_layanan,
                    'price' => (float)$val->harga,
                    'quantity' => 1
                ];
                $items[] = $newItem;
            }
            $countPermintaanPayment = PaymentPermintaan::where('jenis_layanan','homecare')->where('permintaan_id',$request->id_permintaan_hc)->count();
            if($countPermintaanPayment==1 && !empty($permintaan->biaya_ke_lokasi)){
                $items[] = (object)[
                    'name' => 'Ongkos Kirim',
                    'price' => (float)$permintaan->biaya_ke_lokasi,
                    'quantity' => 1
                ];
            }
            $new_invoice = XenditHelpers::createInvoice((string)$payment->id_payment_permintaan, 'Pembayaran Permintaan Layanan Homecare', $payment->nominal, (string)$date_diff, $items)->getData();
            if(!$new_invoice->metaData->code == 200) {
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
            # Update Invoice ID di payment permintaan
            $payment->invoice_id = $new_invoice->response->id;
            $payment->status = $new_invoice->response->status;
            $payment->save();
            // return $new_invoice->response;
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
    public function selesaikanPelayananHC(Request $request) {
        try {
            $data = PermintaanHC::where('id_permintaan_hc', $request->id_permintaan_hc)->first();
            $data->status_pasien = 'selesai';
            $data->save();
            if (!$data) {
                return Help::custom_response(204, "error", 'Gagal menyelesaikan permintaan homecare', null);
            }
            # Log activity
            $activity = Activity::store(Auth::user()->id,"Menyelesaikan layanan homecare");
            if (!$activity) {
                DB::rollback();
                return Help::custom_response(400, "error", "Gagal simpan activity selesaikan homecare", null);
            }
            return Help::custom_response(200, "success", 'Ok', $data);
        } catch (\Throwable $e) {
            $log = ['ERROR SELESAIKAN PELAYANAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function batalOtomatisPermintaanHC(Request $request) {
        try {
            $data = PermintaanHC::where('id_permintaan_hc', $request->id_permintaan_hc)->first();
            $data->status_pasien = 'batal';
            $data->save();
            if (!$data) {
                return Help::custom_response(400, "error", 'Gagal membatalkan permintaan homecare', null);
            }
            # Log activity
            $activity = Activity::store(Auth::user()->id,"Pembatalan otomatis layanan homecare");
            if (!$activity) {
                DB::rollback();
                return Help::custom_response(400, "error", "Gagal simpan activity batal homecare", null);
            }
            return Help::custom_response(200, "success", 'Ok', $data);
        } catch (\Throwable $e) {
            $log = ['ERROR BATAL PELAYANAN HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function riwayatPermintaanHomecare(Request $request) {
        $validate = Validator::make($request->all(),[
            'nik' => 'required'
        ],[
            'nik.required' => 'NIK Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::custom_response(400, "error", $validate->errors()->all()[0], null);
        }
        try{
            $data['permintaan'] = PermintaanHC::with('rating')->where('nik', $request->nik)->orderBy('id_permintaan_hc','DESC')->get();
            if(count($data['permintaan'])==0){
                return Help::custom_response(204, "error", 'Tidak ada riwayat homecare', null);
            }
            return Help::resApi('Berhasil',200,$permintaan);
        } catch (\Throwable $e) {
            $log = ['ERROR GET RIWAYAT HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function saveRatingHc(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_hc' => 'required',
            'star_rating' => 'required',
            'comments' => 'required',
        ],[
            'id_permintaan_hc.required' => 'ID Permintaan Homecare Wajib Di isi',
            'star_rating.required' => 'Bintang Wajib Di isi',
            'comments.required' => 'Komentar Wajib Di isi',
        ]);
        if ($validate->fails()) {
            return Help::custom_response(400, "error", $validate->errors()->all()[0], null);
        }
        try{
            if(!$permintaan = PermintaanHC::where('id_permintaan_hc', $request->id_permintaan_hc)->first()){
                return Help::custom_response(204, "error", 'Permintaan homecare tidak ditemukan', null);
            }
            if(!$rating = Rating::where('permintaan_id', $request->id_permintaan_hc)->where('jenis_layanan', 'homecare')->first()){
                $rating = new Rating;
                $rating->jenis_layanan = 'homecare';
                $rating->permintaan_id = $request->id_permintaan_hc;
            }
            $rating->star_rating = $request->star_rating;
            $rating->comments = $request->comments;
            if (!$rating->save()) {
                return Help::custom_response(400, "error", 'gagal', null);
            }
            # Log activity
            $activity = Activity::store(Auth::user()->id,"Memberi penilaian layanan homecare");
            if (!$activity) {
                DB::rollback();
                return Help::custom_response(400, "error", "Gagal simpan activity rating homecare", null);
            }
            return Help::custom_response(200, "success", 'Ok', $rating);
        } catch (\Throwable $e) {
            $log = ['ERROR SAVE RATING HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function invoiceResep(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_hc' => 'required',
            'diantar' => 'required'
        ],[
            'id_permintaan_hc.required' => 'ID Permintaan Homecare Wajib Di isi',
            'diantar.required' => 'Metode penerimaan obat Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }
        DB::beginTransaction();
        try{
            # Cari data permintaan berdasarkan id_permintaan_hc
            if(!$permintaan = PermintaanHC::where('id_permintaan_hc', $request->id_permintaan_hc)->first()) {
                DB::rollback();
                return Help::resApi('Permintaan Telemedicine Tidak Ditemukan',204);
            }
            # Cari data payment berdasarkan permintaan_id dan jenis layanan homecare
            if (!$payment = PaymentPermintaan::where('permintaan_id', $request->id_permintaan_hc)->where('jenis_layanan', 'eresep_homecare')->first()) {
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
            if(!$resep = ResepObat::where('permintaan_id', $request->id_permintaan_hc)->where('jenis_layanan', 'homecare')->first()){
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
            $new_invoice = XenditHelpers::createInvoice((string)$payment->id_payment_permintaan, 'Pembayaran Eresep Homecare', $total_bayar, (string)$date_diff, $items)->getData();
            if(!$new_invoice->metaData->code == 200) {
                DB::rollback();
                return Help::resApi('Terjadi kesalahan sistem',500);
            }
            # Update Invoice ID di payment permintaan
            $payment->invoice_id = $new_invoice->response->id;
            $payment->status = $new_invoice->response->status;
            $payment->save();
            # Log activity
            $activity = Activity::store(Auth::user()->id,"Create invoice resep homecare");
            if (!$activity) {
                DB::rollback();
                return Help::custom_response(400, "error", "Gagal simpan activity Create invoice resep", null);
            }
            DB::commit();
            return Help::resApi('Pembayaran berhasil dibuat',200,$new_invoice->response->invoice_url);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR INVOICE ERESEP HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function getResep(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_hc' => 'required'
        ],[
            'id_permintaan_hc.required' => 'ID Permintaan Homecare Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }
        try {
            $permintaan = PermintaanHC::where('id_permintaan_hc', $request->id_permintaan_hc)
            ->has('resep_obat')
            ->with('resep_obat')
            ->first();
            if(!$permintaan) {
                return Help::resApi('Permintaan / Resep tidak ditemukan',204);
            }

            $resep = ResepObat::where('permintaan_id', $request->id_permintaan_hc)->where('jenis_layanan', 'homecare');
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
    public function checkByNik($nik, $tanggal) {//check nik apakah sudah digunakan
        $check = PermintaanHC::where('nik','=',$nik)->where('tanggal_kunjungan','=',$tanggal)->count();
        return $check;
    }
}