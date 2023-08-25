<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Auth as Auth;
use App\Http\Controllers\Login\LoginController as Login;
use App\Http\Controllers\Dashboard\DasboardController as Dashboard;
use App\Http\Controllers\MCU\LayananMcuController as LayananMcu;
use App\Http\Controllers\MCU\PermintaanMcuController as PermintaanMcu;
use App\Http\Controllers\MCU\RiwayatMcuController as RiwayatMcu;
use App\Http\Controllers\MCU\PengaturanMcuController as PengaturanMcu;
use App\Http\Controllers\MCU\SyaratMcuController as SyaratMcu;
use App\Http\Controllers\HC\PermintaanHCController as PermintaanHC;
use App\Http\Controllers\HC\RiwayatHCController as RiwayatHC;
use App\Http\Controllers\HC\LayananHCController as LayananHC;
use App\Http\Controllers\HC\PaketHCController as PaketHC;
use App\Http\Controllers\HC\TenagaMedisController as TenagaMedis;
use App\Http\Controllers\HC\PengaturanHCController as PengaturanHC;
use App\Http\Controllers\HC\SyaratHCController as SyaratHC;
use App\Http\Controllers\Pengguna\PenggunaController as Pengguna;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/clear', function() {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('config:cache');
    return 'cleared!';
});

Route::get('/', function () {
	return redirect()->route('dashboard');
});

// START AUTH
Route::get('login', 'App\Http\Controllers\Login\LoginController@index')->name('login');
Route::post('proses_login', 'App\Http\Controllers\Login\LoginController@proses_login')->name('proses_login');
Route::get('logout', 'App\Http\Controllers\Login\LoginController@logout')->name('logout');
// END AUTH
Route::group(['middleware' => ['auth']], function () {
    #Admin
    Route::group(['middleware' => ['cek_login:1']], function () {
        #dashboard Admin
        Route::group(['prefix'=>'admin'],function(){
			Route::get('/dashboard', [Dashboard::class, 'main'])->name('dashboard');
            Route::get('/dashboard/get-data', [Dashboard::class, 'getData'])->name('getDataDashboard');
		});
        #Mcu
        Route::group(['prefix'=>'medical-check-up'],function(){
            // PERMINTAAN MCU
            Route::group(['prefix'=>'permintaan-mcu'],function(){
                Route::get('/', [PermintaanMcu::class, 'main'])->name('mainPermintaanMcu');
                Route::post('/bayar', [PermintaanMcu::class, 'form'])->name('formBayarPermintaanMcu');
                Route::post('/simpan-bayar', [PermintaanMcu::class, 'simpan'])->name('simpanBayarPermintaanMcu');
                Route::post('/proses', [PermintaanMcu::class, 'proses'])->name('prosesPermintaanMcu');
            });

            // RIWAYAT MCU
            Route::group(['prefix'=>'riwayat-mcu'],function(){
                Route::get('/', [RiwayatMcu::class, 'main'])->name('mainRiwayatMcu');
            });

            // LAYANAN MCU
            Route::group(['prefix'=>'layanan-mcu'],function(){
                Route::get('/', [LayananMcu::class, 'main'])->name('mainLayananMcu');
                Route::post('/form', [LayananMcu::class, 'form'])->name('formLayananMcu');
                Route::post('/delete', [LayananMcu::class, 'delete'])->name('deleteLayananMcu');
                Route::post('/save', [LayananMcu::class, 'store'])->name('saveLayananMcu');
            });

            // SYARAT MCU
            Route::group(['prefix'=>'syarat-aturan-mcu'],function(){
                Route::get('/', [SyaratMcu::class, 'main'])->name('mainSyaratMcu');
                Route::post('/save', [SyaratMcu::class, 'store'])->name('saveSyaratMcu');
            });

            // PENGATURAN MCU
            Route::group(['prefix'=>'pengaturan-mcu'],function(){
                Route::get('/', [PengaturanMcu::class, 'form'])->name('formPengaturanMCU');
                Route::get('/get', [PengaturanMcu::class, 'get'])->name('getPengaturanMCU');
                Route::post('/save', [PengaturanMcu::class, 'store'])->name('savePengaturanMcu');
            });
        });
        #Homecare
        Route::group(['prefix'=>'home-care'],function(){
            // PERMINTAAN HC
            Route::group(['prefix'=>'permintaan-homecare'],function(){
                Route::get('/', [PermintaanHC::class, 'main'])->name('mainPermintaanHC');
                Route::post('/form-permintaan-hc', [PermintaanHC::class, 'form'])->name('formPermintaanHC');
                Route::post('/save-permintaan-hc', [PermintaanHC::class, 'save'])->name('savePermintaanHC');
                Route::post('/batal-permintaan-hc', [PermintaanHC::class, 'batal'])->name('batalPermintaanHC');
            });

            // RIWAYAT HC
            Route::group(['prefix'=>'riwayat-homecare'],function(){
                Route::get('/', [RiwayatHC::class, 'main'])->name('mainRiwayatHC');
                Route::post('/export', [RiwayatHC::class, 'export'])->name('exportRiwayatHC');
            });

            // LAYANAN HC
            Route::group(['prefix'=>'layanan-homecare'],function(){
                Route::get('/', [LayananHC::class, 'main'])->name('mainLayananHC');
                Route::post('/form', [LayananHC::class, 'form'])->name('formLayananHC');
                Route::post('/deletane', [LayananHC::class, 'delete'])->name('deleteLayananHC');
                Route::post('/save', [LayananHC::class, 'store'])->name('saveLayananHC');

                // Route::get('/', [LayananHC::class, 'index'])->name('indexLayananHC');
                // Route::get('/layanan-hc/fetch_data', [LayananHC::class, 'fetch_data'])->name('layananhc.fetch_data');
                // Route::post('/layanan-hc/add_data', [LayananHC::class, 'add_data'])->name('layananhc.add_data');
                // Route::post('/layanan-hc/update_data', [LayananHC::class, 'update_data'])->name('layananhc.update_data');
                // Route::post('/layanan-hc/delete_data', [LayananHC::class, 'delete_data'])->name('layananhc.delete_data');
            });

            // PAKET HC
            Route::group(['prefix'=>'paket-homecare'],function(){
                Route::get('/', [PaketHC::class, 'main'])->name('mainPaketHC');
                Route::post('/form', [PaketHC::class, 'form'])->name('formPaketHC');
                Route::post('/delete', [PaketHC::class, 'delete'])->name('deletePaketHC');
                Route::post('/save', [PaketHC::class, 'store'])->name('savePaketHC');
            });

            // TENAGA MEDIS
            Route::group(['prefix'=>'tenaga-medis'],function(){
                Route::get('/', [TenagaMedis::class, 'main'])->name('mainTenagaMedis');
                Route::post('/form', [TenagaMedis::class, 'form'])->name('formTenagaMedis');
                Route::post('/delete', [TenagaMedis::class, 'delete'])->name('deleteTenagaMedis');
                Route::post('/save', [TenagaMedis::class, 'store'])->name('saveTenagaMedis');
            });

            // PENGATURAN HC
            Route::group(['prefix'=>'pengaturan-hc'],function(){
                Route::get('/', [PengaturanHC::class, 'form'])->name('formPengaturanHC');
                Route::get('/get', [PengaturanHC::class, 'get'])->name('getPengaturanHC');
                Route::post('/save', [PengaturanHC::class, 'store'])->name('savePengaturanHC');
            });

            // SYARAT HC
            Route::group(['prefix'=>'syarat-aturan-hc'],function(){
                Route::get('/', [SyaratHC::class, 'main'])->name('mainSyaratHC');
                Route::post('/save', [SyaratHC::class, 'store'])->name('saveSyaratHC');
            });
        });
        #Pengguna
        Route::group(['prefix'=>'pengguna'],function(){
			Route::get('/', [Pengguna::class, 'main'])->name('mainPengguna');
            Route::post('/form', [Pengguna::class, 'form'])->name('formPengguna');
            Route::post('/delete', [Pengguna::class, 'delete'])->name('deletePengguna');
            Route::post('/save', [Pengguna::class, 'store'])->name('savePengguna');
		});
    });
    #Admin MCU
    Route::group(['middleware' => ['cek_login:2']], function () {
        #Dashboard admin mcu
        Route::group(['prefix'=>'admin-mcu'],function(){
			Route::get('/dashboard', [Dashboard::class, 'main'])->name('dashboardMcu');
            Route::get('/dashboard/get-data', [Dashboard::class, 'getData'])->name('getDataDashboardMcu');
		});
    });
    #Admin HOMECARE
    Route::group(['middleware' => ['cek_login:3']], function () {
        Route::group(['prefix'=>'admin-homecare'],function(){
			Route::get('/dashboard', [Dashboard::class, 'main'])->name('dashboardHc');
            Route::get('/dashboard/get-data', [Dashboard::class, 'getData'])->name('getDataDashboardHc');
		});
    });
    #Admin PSC
    Route::group(['middleware' => ['cek_login:4']], function () {
        Route::group(['prefix'=>'admin-psc'],function(){
			Route::get('/dashboard', [Dashboard::class, 'main'])->name('dashboardPsc');
            Route::get('/dashboard/get-data', [Dashboard::class, 'getData'])->name('getDataDashboardPsc');
		});
    });
});
Route::group(['prefix' => 'invoice'], function() {
    Route::get('/invoice_mcu/{id}', [PermintaanMcu::class, 'invoiceMcu'])->name('invoiceMcu');
});
