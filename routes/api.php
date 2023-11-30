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
use App\Http\Controllers\API\ApiArtikelController as ApiArtikel;

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
Route::middleware('auth:sanctum')->get('ping',function(){
    return 'PIG';
});
Route::group(['middleware' => ['auth:sanctum']], function () {
    # user access pasien
    Route::group(['middleware' => ['cek_login:pasien']], function () {
        Route::group(array('prefix' => 'pendaftaran-mcu'), function(){
            Route::get('/get_layanan_mcu/{kategori}/{jenis}', [ApiPendaftaranMcu::class, 'getLayananMcu']);
            Route::get('/get_syarat_aturan', [ApiPendaftaranMcu::class, 'getSyaratAturan']);
            Route::get('/detail_layanan_mcu/{id}', [ApiPendaftaranMcu::class, 'getDetailLayananMcu']);
            Route::post('/pesan_jadwal_mcu', [ApiPendaftaranMcu::class, 'pesanJadwalMcu']);
            Route::get('/list_permintaan_mcu/{id}', [ApiPendaftaranMcu::class, 'getListPermintaanMCU']);
            // Route::get('/list_pembayaran_mcu/{id}', [ApiPendaftaranMcu::class, 'getListPermintaanHC']);
            Route::post('/invoice_mcu', [ApiPendaftaranMcu::class, 'invoiceMCU']);
            Route::post('/batal_otomatis_mcu', [ApiPendaftaranMcu::class, 'batalOtomatisPermintaanMCU']);
            Route::post('/selesaikan_permintaan_mcu', [ApiPendaftaranMcu::class, 'selesaikanPermintaanMCU']);
            Route::post('/riwayat_permintaan_mcu/{params}', [ApiPendaftaranMcu::class, 'riwayatPermintaanMCU']);
            Route::post('/save_rating_mcu', [ApiPendaftaranMcu::class, 'saveRatingMCU']);
            // Route::get('/get_payment_mcu', [ApiPendaftaranMcu::class, 'getPaymentMCU']);
            // Route::post('/process_transaksi_mcu', [ApiPendaftaranMcu::class, 'transaksiProcessMCU']);
        });
        # Start Telemedicine
        Route::group(array('prefix' => 'pendaftaran-telemedicine'), function(){
            Route::post('/pesan_jadwal_telemedicine', [ApiPendaftaranTelemedicine::class, 'pesanJadwalTelemedicine']);
            Route::get('/get_form_permintaan_telemedicine', [ApiPendaftaranTelemedicine::class, 'getFormPermintaan']);
            Route::post('/get_list_telemedicine', [ApiPendaftaranTelemedicine::class, 'getListTelemedicine']);
            Route::post('/get_list_pelayanan_telemedicine', [ApiPendaftaranTelemedicine::class, 'getListPelayananTelemedicine']);
            Route::post('/riwayat_permintaan_telemedicine', [ApiPendaftaranTelemedicine::class, 'riwayatPermintaanTelemedicine']);
            Route::get('/get_poli_telemedicine', [ApiPendaftaranTelemedicine::class, 'getPoli']);
            Route::post('/get_dokter_telemedicine', [ApiPendaftaranTelemedicine::class, 'getDokter']);
            Route::post('/get_jadwal_dokter_telemedicine', [ApiPendaftaranTelemedicine::class, 'getJadwalDokter']);
            Route::get('/get_permintaan_telemedicine', [ApiPendaftaranTelemedicine::class, 'getPermintaan']);
            Route::post('/batalkan_permintaan_telemedicine', [ApiPendaftaranTelemedicine::class, 'batalkanPermintaan']);
            Route::post('/invoice', [ApiPendaftaranTelemedicine::class, 'invoice']);
            Route::get('/get_biaya_telemedicine', [ApiPendaftaranTelemedicine::class, 'getBiayaTelemedicine']);
            Route::post('/get_resep', [ApiPendaftaranTelemedicine::class, 'getResep']);
            Route::post('/cek_antar', [ApiPendaftaranTelemedicine::class, 'cekAntar']);
            Route::post('/invoice_resep', [ApiPendaftaranTelemedicine::class, 'invoiceResep']);
            Route::post('/save_rating', [ApiPendaftaranTelemedicine::class, 'saveRating']);
            Route::post('/selesaikan_telemedicine', [ApiPendaftaranTelemedicine::class, 'selesaikan']);
        });
        # End Telemedicine
        # Start Homecare
        Route::group(array('prefix' => 'pendaftaran-homecare'), function(){
            Route::get('/get_layanan_hc', [ApiPendaftaranHC::class, 'getLayananHC']);
            Route::get('/get_syarat_aturan_hc', [ApiPendaftaranHC::class, 'getSyaratAturanHC']);
            Route::post('/detail_layanan_hc', [ApiPendaftaranHC::class, 'getDetailLayananHC']);
            Route::post('/pesan_jadwal_hc', [ApiPendaftaranHC::class, 'pesanJadwalHC']);
            Route::get('/list_permintaan_hc/{id}', [ApiPendaftaranHC::class, 'getListPermintaanHC']);
            Route::post('/selesaikan_pelayanan_hc', [ApiPendaftaranHC::class, 'selesaikanPelayananHC']);
            Route::post('/riwayat_permintaan_hc', [ApiPendaftaranHC::class, 'riwayatPermintaanHomecare']);
            // Route::get('/get_permintaan_tm/{id}', [ApiPendaftaranHC::class, 'getPermintaanTM']);
            // Route::post('/update_profile_tm', [ApiPendaftaranHC::class, 'updateProfileTM']);
            // Route::post('/get_profile_tm', [ApiPendaftaranHC::class, 'getProfileTM']);
            Route::post('/save_rating_hc', [ApiPendaftaranHC::class, 'saveRatingHC']);
            // Route::get('/get_payment_hc', [ApiPendaftaranHC::class, 'getPaymentHC']);
            Route::post('/invoice_hc', [ApiPendaftaranHC::class, 'invoiceHC']);
            Route::post('/add_layanan_homecare', [ApiPendaftaranHC::class, 'addLayananHomecare']);
            // Route::post('/process_transaksi_hc', [ApiPendaftaranHC::class, 'transaksiProcessHC']);
            // Route::post('/callback_hc', [ApiPendaftaranHC::class, 'callbackHC']);
            Route::post('/invoice_resep', [ApiPendaftaranHC::class, 'invoiceResep']);
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
        Route::group(array('prefix' => 'artikel-kesehatan'), function(){
            Route::get('/get-limit', [ApiArtikel::class, 'getLimit']);
            Route::get('/get-all', [ApiArtikel::class, 'getAll']);
            Route::get('/get-detail/{id}', [ApiArtikel::class, 'getDetail']);
        });
        # End Pengaturan
        # Start Pengaturan
        Route::group(array('prefix' => 'pengaturan'), function(){
            Route::get('/get-data-login/{id}', [ApiSetting::class, 'getDataLogin']);
            Route::post('/update-data-login', [ApiSetting::class, 'updateDataLogin']);
            Route::get('/get-profile-user/{id}', [ApiSetting::class, 'getProfileUser']);
            Route::post('/update-profile-user', [ApiSetting::class, 'updateProfileUser']);
        });
        # End Pengaturan
    });
    Route::group(array('prefix' => 'realtime-cost'), function(){
        Route::get('/get_rtc_umum', [ApiRealTimeCost::class, 'getRealtimeUmum']);
    });

    # Start Dokter
    # user access dokter
    Route::group(['middleware' => ['cek_login:dokter']], function () {
        Route::group(array('prefix' => 'dokter-pelayanan'), function(){
            Route::post('/get_permintaan_telemedicine', [ApiPelayananDokter::class, 'getPermintaanTelemedicine']);
            Route::post('/get_riwayat_telemedicine', [ApiPelayananDokter::class, 'getRiwayatTelemedicine']);
            Route::post('/form_resep_telemedicine', [ApiPelayananDokter::class, 'formResepTelemedicine']);
            Route::post('/save_resep_telemedicine', [ApiPelayananDokter::class, 'saveResepTelemedicine']);
            Route::post('/layani_telemedicine', [ApiPelayananDokter::class, 'layaniTelemedicine']);
            Route::get('/cari_obat/{q}', [ApiPelayananDokter::class, 'cariObat']);
            Route::post('/get_penilaian', [ApiPelayananDokter::class, 'getPenilaian']);
        });
    });
    # End Dokter

    # Start Perawat
    # user access perawat
    Route::group(['middleware' => ['cek_login:perawat']], function () {
        Route::group(array('prefix' => 'perawat-pelayanan'), function(){
            Route::post('/get_pelayanan_perawat', [ApiPelayananPerawat::class, 'getPerawatPelayanan']);
            Route::post('/get_riwayat_perawat', [ApiPelayananPerawat::class, 'getRiwayatPelayanan']);
            Route::post('/form_resep_telemedicine', [ApiPelayananPerawat::class, 'formResepTelemedicine']);
            Route::post('/form_resep_homecare', [ApiPelayananPerawat::class, 'formResepHomecare']);
            Route::post('/save_resep_telemedicine', [ApiPelayananPerawat::class, 'saveResepTelemedicine']);
            Route::post('/save_resep_homecare', [ApiPelayananPerawat::class, 'saveResepHomecare']);
            Route::post('/layani_telemedicine', [ApiPelayananPerawat::class, 'layaniTelemedicine']);
            Route::get('/cari_obat/{q}', [ApiPelayananPerawat::class, 'cariObat']);
            Route::post('/get_penilaian', [ApiPelayananPerawat::class, 'getPenilaian']);
        });
    });
    # End Perawat
});
