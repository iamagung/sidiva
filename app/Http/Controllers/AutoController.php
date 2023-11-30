<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanTelemedicine;
use App\Models\PermintaanHC;
use App\Models\PermintaanMcu;
use App\Models\PaymentPermintaan;
use App\Models\ResepObat;
use App\Helpers\Helpers as Help;
use DB;

class AutoController extends Controller
{
    public function cekPembayaranBatal() {
        DB::beginTransaction();
        try {
            $sekarang = date('Y-m-d H:i:s');
            $pembayaran = PaymentPermintaan::whereIn('status', ['UNCONFIRMED', 'PENDING'])
                ->where('tgl_expired', '<', $sekarang)
                ->get();
            if (count($pembayaran)>0) {
                foreach ($pembayaran as $key => $value) {
                    if($value->jenis_layanan == 'telemedicine') {
                        if($permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $value->permintaan_id)->first()) {
                            $permintaan->status_pembayaran = 'batal';
                            $permintaan->status_pasien = 'batal';
                            if(!$permintaan->save()) {
                                DB::rollback();
                                return Help::resApi('Gagal saat memperbarui status pembayaran', 400);
                            }
                            if($payment = PaymentPermintaan::where('permintaan_id', $value->permintaan_id)->where('jenis_layanan', 'telemedicine')->first()) {
                                $payment->status = 'EXPIRED';
                                if(!$payment->save()) {
                                    DB::rollback();
                                    return Help::resApi('Gagal saat memperbarui status pembayaran', 400);
                                }
                            }
                        }
                    }
                    if($value->jenis_layanan == 'homecare') {
                        if($permintaan = PermintaanHC::where('id_permintaan_hc', $value->permintaan_id)->first()) {
                            $permintaan->status_pembayaran = 'batal';
                            $permintaan->status_pasien = 'batal';
                            if(!$permintaan->save()) {
                                DB::rollback();
                                return Help::resApi('Gagal saat memperbarui status pembayaran', 400);
                            }
                            if($payment = PaymentPermintaan::where('permintaan_id', $value->permintaan_id)->where('jenis_layanan', 'homecare')->first()) {
                                $payment->status = 'EXPIRED';
                                if(!$payment->save()) {
                                    DB::rollback();
                                    return Help::resApi('Gagal saat memperbarui status pembayaran', 400);
                                }
                            }
                        }
                    }
                    if($value->jenis_layanan == 'mcu') {
                        if($permintaan = PermintaanMcu::where('id_permintaan', $value->permintaan_id)->first()) {
                            $permintaan->status_pembayaran = 'batal';
                            $permintaan->status_pasien = 'batal';
                            if(!$permintaan->save()) {
                                DB::rollback();
                                return Help::resApi('Gagal saat memperbarui status pembayaran', 400);
                            }
                            if($payment = PaymentPermintaan::where('permintaan_id', $value->permintaan_id)->where('jenis_layanan', 'mcu')->first()) {
                                $payment->status = 'EXPIRED';
                                if(!$payment->save()) {
                                    DB::rollback();
                                    return Help::resApi('Gagal saat memperbarui status pembayaran', 400);
                                }
                            }
                        }
                    }
                    if($value->jenis_layanan == 'eresep_telemedicine') {
                        if($resep = ResepObat::where('permintaan_id', $value->permintaan_id)->where('jenis_layanan', 'telemedicine')->first()) {
                            $resep->status_pembayaran = 'batal';
                            if(!$resep->save()) {
                                DB::rollback();
                                return Help::resApi('Gagal saat memperbarui status pembayaran', 400);
                            }
                            if($payment = PaymentPermintaan::where('permintaan_id', $value->permintaan_id)->where('jenis_layanan', 'eresep_telemedicine')->first()) {
                                $payment->status = 'EXPIRED';
                                if(!$payment->save()) {
                                    DB::rollback();
                                    return Help::resApi('Gagal saat memperbarui status pembayaran', 400);
                                }
                            }
                        }
                    }
                }
                DB::commit();
                return Help::resApi('Berhasil memperbarui '.count($pembayaran).' Pembayaran', 200);
            } else {
                DB::rollback();
                return Help::resApi('Tidak ada pembayaran yang diperbaruhi', 204);
            }
        } catch (\Throwable $e) {
            DB::rollback();
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR CEK PEMBAYARAN BATAL PERMINTAAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function cekPermintaanSelesai() {
        try {
            DB::beginTransaction();
            $sekarang = date('Y-m-d H:i:s');
            $permintaanTele = PermintaanTelemedicine::where('status_pasien', 'proses')
                ->whereRaw('datediff(cast(concat(tanggal_kunjungan, " ", substring(jadwal_dokter, 0, 5)) as datetime),?) <= -1',[$sekarang])
                ->update(['status_pasien'=>'selesai']);
            // $permintaanHC = PermintaanHC::where('status_pasien','proses')
            //     ->whereRaw('datediff(cast(concat(tanggal_kunjungan, " ", substring(waktu_layanan, 0, 5)) as datetime),?) <= -1',[$sekarang])
            //     ->update(['status_pasien'=>'selesai']);
            $permintaanMcu = PermintaanMcu::where('status_pasien','proses')
                ->whereRaw('datediff(cast(concat(date_mcu, " ", time_mcu) as datetime),?) <= -1',[$sekarang])
                ->update(['status_pasien'=>'selesai']);
                // ->selectRaw('datediff(cast(concat(tanggal_kunjungan, " ", substring(jadwal_dokter, 0, 5)) as datetime),?) as sekarang',[$sekarang])->get();
            DB::commit();
            return Help::resApi('Berhasil memperbarui Permintaan Telemedicine ('.$permintaanTele.'), Homecare ('.'belum'.'), MCU ('.$permintaanMcu.')', 200);
        } catch (\Throwable $e) {
            DB::rollback();
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR CEK PERMINTAAN AUTO SELESAI ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
