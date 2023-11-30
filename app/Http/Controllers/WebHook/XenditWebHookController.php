<?php

namespace App\Http\Controllers\WebHook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentPermintaan;
use App\Models\PermintaanTelemedicine;
use App\Models\PermintaanHC;
use App\Models\PermintaanMcu;
use App\Models\LayananPermintaanHc;
use App\Models\ResepObat;
use App\Helpers\Helpers as Help;
use Config, DB;

class XenditWebHookController extends Controller
{
    public function __construct() {
        $this->callback_token = config('xendit.callback_token');
    }

    # Cek apakah token sesuai dari xendit
    function isXendit($token) {
        return $this->callback_token === $token ? true : false;
    }

    public function invoice(Request $request) {
        $token = (null != $request->header('x-callback-token')) ? $request->header('x-callback-token') : "";
        if(!Self::isXendit($token)){
            return Help::resApi('Token tidak sesuai',400);
        }
        try {
            DB::beginTransaction();
            if($payment = PaymentPermintaan::where('invoice_id', $request->id)->where('id_payment_permintaan', $request->external_id)->first()) {
                # Karena terdapat 3 jenis pelayanan
                # Jika payment untuk pelayanan telemedicine
                if($payment->jenis_layanan == 'telemedicine') {
                    if($permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $payment->permintaan_id)->first()){
                        if($request->status == 'PAID' || $request->status == 'SETTLED'){
                            $permintaan->status_pembayaran = 'lunas';
                            $permintaan->save();
                        }
                        if($request->status == 'PENDING'){
                            $permintaan->status_pembayaran = 'pending';
                            $permintaan->save();
                        }
                        if($request->status == 'EXPIRED'){
                            $permintaan->status_pembayaran = 'batal';
                            $permintaan->status_pasien = 'batal';
                            $permintaan->save();
                        }
                    }
                }
                # Jika payment untuk pelayanan homecare
                if($payment->jenis_layanan == 'homecare') {
                    if($permintaan = PermintaanHC::where('id_permintaan_hc', $payment->permintaan_id)->first()){
                        if($request->status == 'PAID' || $request->status == 'SETTLED'){
                            $permintaan->status_pembayaran = 'paid';
                            $permintaan->save();

                            $layananPermintaan = LayananPermintaanHc::where('permintaan_id',$permintaan->id_permintaan_hc)->update(['status_bayar'=>true]);
                            if (!$layananPermintaan) {
                                DB::rollback();
                                return Help::resApi('Gagal simpan layanan permintaan homecare', 400);
                            }
                        }
                        if($request->status == 'PENDING'){
                            $permintaan->status_pembayaran = 'pending';
                            $permintaan->save();
                        }
                    }
                }
                # Jika payment untuk pelayanan mcu
                if($payment->jenis_layanan == 'mcu') {
                    if($permintaan = PermintaanMcu::where('id_permintaan', $payment->permintaan_id)->first()){
                        if($request->status == 'PAID' || $request->status == 'SETTLED'){
                            $permintaan->status_pembayaran = 'paid';
                            $permintaan->save();
                        }
                        if($request->status == 'PENDING'){
                            $permintaan->status_pembayaran = 'pending';
                            $permintaan->save();
                        }
                    }
                }
                # Jika payment untuk pelayanan eresep
                // if($payment->jenis_layanan == 'eresep') {
                //     if($permintaan = PermintaanMcu::where('id_permintaan', $payment->permintaan_id)->first()){
                //         if($request->status == 'PAID' || $request->status == 'SETTLED'){
                //             $permintaan->status_pembayaran = 'paid';
                //             $permintaan->save();
                //         }
                //         if($request->status == 'PENDING'){
                //             $permintaan->status_pembayaran = 'pending';
                //             $permintaan->save();
                //         }
                //     }
                // }
                # Jika payment untuk pelayanan eresep
                if($payment->jenis_layanan == 'eresep_telemedicine') {
                    if($permintaan = ResepObat::where('permintaan_id', $payment->permintaan_id)->where('jenis_layanan', 'telemedicine')->first()){
                        if($request->status == 'PAID' || $request->status == 'SETTLED'){
                            $permintaan->status_pembayaran = 'lunas';
                            $permintaan->save();
                        }
                        if($request->status == 'PENDING'){
                            $permintaan->status_pembayaran = 'belum';
                            $permintaan->save();
                        }
                        if($request->status == 'EXPIRED'){
                            $permintaan->status_pembayaran = 'batal';
                            $permintaan->save();
                        }
                    }
                }
                if($payment->jenis_layanan == 'eresep_homecare') {
                    if($permintaan = ResepObat::where('permintaan_id', $payment->permintaan_id)->where('jenis_layanan', 'homecare')->first()){
                        if($request->status == 'PAID' || $request->status == 'SETTLED'){
                            $permintaan->status_pembayaran = 'lunas';
                            $permintaan->save();
                        }
                        if($request->status == 'PENDING'){
                            $permintaan->status_pembayaran = 'belum';
                            $permintaan->save();
                        }
                        if($request->status == 'EXPIRED'){
                            $permintaan->status_pembayaran = 'batal';
                            $permintaan->save();
                        }
                    }
                }
                $payment->status = $request->status;
                if(in_array($request->status,['PAID','SETTLED'])){
                    $payment->tgl_lunas = date("Y-m-d H:i:s", strtotime($request->paid_at));
                } else {
                    $payment->tgl_lunas = null;
                }
                if($payment->save()){

                    DB::commit();
                    return Help::resApi('Data berhasil masuk', 200);
                }
            }
            DB::rollback();
            return Help::resApi('Data gagal disimpan', 400);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            DB::rollback();
            $log = ['ERROR WEBHOOK INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
