<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiAuthController as ApiAuth;
use App\Http\Controllers\API\ApiPendaftaranMcuController as ApiPendaftaranMcu;
use App\Http\Controllers\API\ApiPendaftaranHcController as ApiPendaftaranHC;
use App\Http\Controllers\API\ApiRealtimecostController as ApiRealTimeCost;
use App\Http\Controllers\API\ApiPendaftaranAmbulanceController as ApiPendaftaranAmbulance;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(array('prefix' => 'auth'), function(){
    Route::post('/register', [ApiAuth::class, 'registerPasien']);
    Route::post('/sendotp', [ApiAuth::class, 'sendOtp']);
});
Route::group(array('prefix' => 'pendaftaran-mcu'), function(){
    Route::get('/get_layanan_mcu/{param}', [ApiPendaftaranMcu::class, 'getLayananMcu']);
    Route::get('/get_syarat_aturan', [ApiPendaftaranMcu::class, 'getSyaratAturan']);
    Route::post('/detail_layanan_mcu/{id}', [ApiPendaftaranMcu::class, 'getDetailLayananMcu']);
    Route::post('/pesan_jadwal_mcu', [ApiPendaftaranMcu::class, 'pesanJadwalMcu']);
    Route::get('/list_pembayaran_mcu/{id}', [ApiPendaftaranMcu::class, 'getListPembayaranMcu']);
    // Route::get('/get_invoice_mcu/{id}', [ApiPendaftaranMcu::class, 'getInvoiceMcu']);
    Route::post('/selesaikan_permintaan_mcu', [ApiPendaftaranMcu::class, 'selesaikanPermintaanMCU']);
    Route::post('/riwayat_permintaan_mcu/{params}', [ApiPendaftaranMcu::class, 'riwayatPermintaanMCU']);
    Route::post('/batalkan_permintaan_mcu', [ApiPendaftaranMcu::class, 'batalPermintaanMCU']);
    Route::post('/save_rating_mcu', [ApiPendaftaranMcu::class, 'saveRatingMCU']);
    Route::get('/get_payment_mcu', [ApiPendaftaranMcu::class, 'getPaymentMCU']);
    Route::post('/process_transaksi_mcu', [ApiPendaftaranMcu::class, 'transaksiProcessMCU']);
    Route::post('/callback_mcu', [ApiPendaftaranMcu::class, 'callbackMCU']);
});
# Start Homecare
Route::group(array('prefix' => 'pendaftaran-hc'), function(){
    Route::get('/get_layanan_hc', [ApiPendaftaranHC::class, 'getLayananHC']);
    // Route::get('/get_paket_hc', [ApiPendaftaranHC::class, 'getPaketHC']);
    Route::get('/get_syarat_aturan_hc', [ApiPendaftaranHC::class, 'getSyaratAturanHC']);
    Route::post('/detail_layanan_hc', [ApiPendaftaranHC::class, 'getDetailLayananHC']);
    Route::post('/pesan_jadwal_hc', [ApiPendaftaranHC::class, 'pesanJadwalHC']);
    Route::post('/selesaikan_pelayanan_hc', [ApiPendaftaranHC::class, 'selesaikanPelayananHC']);
    Route::get('/get_permintaan_tm/{id}', [ApiPendaftaranHC::class, 'getPermintaanTM']);
    Route::post('/riwayat_permintaan_tm', [ApiPendaftaranHC::class, 'riwayatPermintaanTM']);
    Route::post('/update_profile_tm', [ApiPendaftaranHC::class, 'updateProfileTM']);
    Route::post('/get_profile_tm', [ApiPendaftaranHC::class, 'getProfileTM']);
    Route::post('/save_rating_hc', [ApiPendaftaranHC::class, 'saveRatingHC']);
    Route::get('/get_payment_hc', [ApiPendaftaranMcu::class, 'getPaymentHC']);
    Route::post('/process_transaksi_hc', [ApiPendaftaranMcu::class, 'transaksiProcessHC']);
    Route::post('/callback_hc', [ApiPendaftaranMcu::class, 'callbackHC']);
});
# End Homecare
# Start Ambulance
Route::group(array('prefix' => 'pendaftaran-ambulance'), function(){
    Route::post('/pesan-jadwal-ambulance', [ApiPendaftaranAmbulance::class, 'pesanJadwalAmbulance']);
});
# End Ambulance
Route::group(array('prefix' => 'realtime-cost'), function(){
    Route::get('/get_rtc_umum', [ApiRealTimeCost::class, 'getRealtimeUmum']);
});
