<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiAuthController as ApiAuth;
use App\Http\Controllers\API\ApiPendaftaranMcuController as ApiPendaftaranMcu;
use App\Http\Controllers\API\ApiPendaftaranHcController as ApiPendaftaranHC;
use App\Http\Controllers\API\ApiSettingController as ApiSetting;
use App\Http\Controllers\API\ApiRealtimecostController as ApiRealTimeCost;
use App\Http\Controllers\API\ApiPendaftaranAmbulanceController as ApiPendaftaranAmbulance;
use App\Http\Controllers\API\ApiPendaftaranTelemedicineController as ApiPendaftaranTelemedicine;
use App\Http\Controllers\API\ApiPelayananDokterController as ApiPelayananDokter;
use App\Http\Controllers\API\ApiPelayananPerawatController as ApiPelayananPerawat;

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

Route::post('/register', [ApiAuth::class, 'register']);
Route::post('/login', [ApiAuth::class, 'login']);
Route::post('/sendotp', [ApiAuth::class, 'sendOtp']);
Route::post('/verifyotp', [ApiAuth::class, 'verifyOtp']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::middleware('auth:api')->get('/profile', function () { #hak akses untuk user harus login
//     return auth()->user();
// });
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
# Start Telemedicine
Route::group(array('prefix' => 'pendaftaran-telemedicine'), function(){
    Route::post('/pesan_jadwal_telemedicine', [ApiPendaftaranTelemedicine::class, 'pesanJadwalTelemedicine']);
    Route::post('/get_list_telemedicine', [ApiPendaftaranTelemedicine::class, 'getListTelemedicine']);
    Route::post('/riwayat_permintaan_telemedicine', [ApiPendaftaranTelemedicine::class, 'riwayatPermintaanTelemedicine']);
    Route::get('/get_poli_telemedicine', [ApiPendaftaranTelemedicine::class, 'getPoli']);
    Route::post('/get_dokter_telemedicine', [ApiPendaftaranTelemedicine::class, 'getDokter']);
    Route::post('/get_jadwal_dokter_telemedicine', [ApiPendaftaranTelemedicine::class, 'getJadwalDokter']);
    Route::get('/get_permintaan_telemedicine', [ApiPendaftaranTelemedicine::class, 'getPermintaan']);
    Route::get('/tes', [ApiPendaftaranTelemedicine::class, 'tes']);
});
# End Telemedicine
# Start Homecare
Route::group(array('prefix' => 'pendaftaran-homecare'), function(){
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
    Route::get('/get-syarat-aturan-ambulance', [ApiPendaftaranAmbulance::class, 'getSyaratAturanAmbulance']);
    Route::get('/get-layanan-ambulance', [ApiPendaftaranAmbulance::class, 'getLayananAmbulance']);
});
# End Ambulance
# Start Pengaturan
Route::group(array('prefix' => 'pengaturan'), function(){
    Route::get('/get-data-login/{id}', [ApiSetting::class, 'getDataLogin']);
    Route::post('/update-data-login', [ApiSetting::class, 'updateDataLogin']);
    Route::get('/get-profile-user/{id}', [ApiSetting::class, 'getProfileUser']);
    Route::post('/update-profile-user', [ApiSetting::class, 'updateProfileUser']);
});
# End Pengaturan
Route::group(array('prefix' => 'realtime-cost'), function(){
    Route::get('/get_rtc_umum', [ApiRealTimeCost::class, 'getRealtimeUmum']);
});
# Start Dokter
Route::group(array('prefix' => 'dokter-pelayanan'), function(){
    Route::post('/get_permintaan_telemedicine', [ApiPelayananDokter::class, 'getPermintaanTelemedicine']);
    Route::post('/get_riwayat_telemedicine', [ApiPelayananDokter::class, 'getRiwayatTelemedicine']);
    Route::post('/form_resep_telemedicine', [ApiPelayananDokter::class, 'formResepTelemedicine']);
    Route::post('/save_resep_telemedicine', [ApiPelayananDokter::class, 'saveResepTelemedicine']);
    Route::get('/cari_obat/{q}', [ApiPelayananDokter::class, 'cariObat']);
});
# End Dokter
# Start Perawat
Route::group(array('prefix' => 'perawat-pelayanan'), function(){
    Route::post('/get_pelayanan_perawat', [ApiPelayananPerawat::class, 'getPerawatPelayanan']);
    Route::post('/get_riwayat_perawat', [ApiPelayananPerawat::class, 'getRiwayatPelayanan']);
    Route::post('/form_resep_telemedicine', [ApiPelayananPerawat::class, 'formResepTelemedicine']);
    Route::post('/save_resep_telemedicine', [ApiPelayananPerawat::class, 'saveResepTelemedicine']);
});
# End Perawat
