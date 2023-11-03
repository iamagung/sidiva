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
use App\Http\Controllers\Psc\PermintaanPscController as PermintaanPsc;
use App\Http\Controllers\Psc\RiwayatPscController as RiwayatPsc;
use App\Http\Controllers\Psc\LayananPscController as LayananPsc;
use App\Http\Controllers\Psc\EmergencyPscController as EmergencyPsc;
use App\Http\Controllers\Psc\DriverPscController as DriverPsc;
use App\Http\Controllers\Psc\SyaratPscController as SyaratPsc;
use App\Http\Controllers\Psc\PengaturanPscController as PengaturanPsc;
use App\Http\Controllers\HC\PermintaanHCController as PermintaanHC;
use App\Http\Controllers\HC\RiwayatHCController as RiwayatHC;
use App\Http\Controllers\HC\LayananHCController as LayananHC;
use App\Http\Controllers\HC\PaketHCController as PaketHC;
use App\Http\Controllers\HC\TenagaMedisController as TenagaMedis;
use App\Http\Controllers\HC\PengaturanHCController as PengaturanHC;
use App\Http\Controllers\HC\SyaratHCController as SyaratHC;
use App\Http\Controllers\Pengguna\PenggunaController as Pengguna;
use App\Http\Controllers\Admin\LaporanLayananController as LapLayanan;
use App\Http\Controllers\Admin\LaporanKeuanganController as LapKeuangan;
use App\Http\Controllers\Telemedicine\PermintaanTelemedicineController as PermintaanTelemedicine;
use App\Http\Controllers\Telemedicine\RiwayatTelemedicineController as RiwayatTelemedicine;
use App\Http\Controllers\Telemedicine\LayananTelemedicineController as LayananTelemedicine;
use App\Http\Controllers\Telemedicine\SyaratTelemedicineController as SyaratTelemedicine;
use App\Http\Controllers\Telemedicine\PengaturanTelemedicineController as PengaturanTelemedicine;
use App\Http\Controllers\Telemedicine\TenagaMedisController as TenagaMedisTelemedicine;

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

# START AUTH
Route::get('login', [Login::class, 'index'])->name('login');
Route::post('proses_login', [Login::class, 'prosesLogin'])->name('proses_login');
Route::get('logout', [Login::class, 'logout'])->name('logout');
# END AUTH
Route::group(['middleware' => ['auth']], function () {
    #dashboard
    Route::group(['prefix'=>'admin'],function(){
        Route::get('/dashboard', [Dashboard::class, 'main'])->name('dashboard');
        Route::get('/dashboard/get-data', [Dashboard::class, 'getData'])->name('getDataDashboard');
    });
    #Admin
    Route::group(['middleware' => ['cek_login:admin']], function () {
        #Pengguna
        Route::group(['prefix'=>'pengguna'],function(){
			Route::get('/', [Pengguna::class, 'main'])->name('mainPengguna');
            // Route::post('/form', [Pengguna::class, 'form'])->name('formPengguna');
            Route::post('/modalForm', [Pengguna::class, 'form'])->name('formPengguna');
            Route::post('/delete', [Pengguna::class, 'delete'])->name('deletePengguna');
            Route::post('/save', [Pengguna::class, 'store'])->name('savePengguna');
		});
        # Ambulance
        Route::group(['prefix'=>'psc'],function(){
            Route::get('/', [Pengguna::class, 'main'])->name('mainPsc');
        });
        # Call emergency
        Route::group(['prefix'=>'emergency'],function(){
            Route::get('/', [EmergencyPsc::class, 'main'])->name('mainEmergencyPsc');
        });
        # Driver emergency
        Route::group(['prefix'=>'driver'],function(){
            Route::get('/', [DriverPsc::class, 'main'])->name('mainDriverPsc');
            Route::post('/form', [DriverPsc::class, 'form'])->name('formDriverPsc');
            Route::post('/delete', [DriverPsc::class, 'delete'])->name('deleteDriverPsc');
            Route::post('/save', [DriverPsc::class, 'store'])->name('saveDriverPsc');
        });
        // Route::group(['prefix'=>'pendaftaran-psc'],function(){
        //     // PERMINTAAN AMBULANCE
        //     Route::group(['prefix'=>'permintaan'],function(){
        //         Route::get('/', [PermintaanPsc::class, 'main'])->name('mainPermintaanPsc');
        //         Route::post('/modalForm', [PermintaanPsc::class, 'form'])->name('formPermintaanPsc');
        //         // Route::post('/delete', [LayananPsc::class, 'delete'])->name('deleteLayananPc');
        //         // Route::post('/save', [LayananPsc::class, 'store'])->name('saveLayananPsc');
        //     });
        //     // RIWAYAT AMBULANCE
        //     Route::group(['prefix'=>'riwayat'],function(){
        //         Route::get('/', [RiwayatPsc::class, 'main'])->name('mainRiwayatPsc');
        //     });
        //     // LAYANAN AMBULANCE
        //     Route::group(['prefix'=>'layanan'],function(){
        //         Route::get('/', [LayananPsc::class, 'main'])->name('mainLayananPsc');
        //         Route::post('/modalForm', [LayananPsc::class, 'form'])->name('formAddPsc');
        //         Route::post('/delete', [LayananPsc::class, 'delete'])->name('deleteLayananPc');
        //         Route::post('/save', [LayananPsc::class, 'store'])->name('saveLayananPsc');
        //     });
        //     // CALL EMERGENCY
        //     Route::group(['prefix'=>'emergency'],function(){
        //         Route::get('/', [EmergencyPsc::class, 'main'])->name('mainEmergencyPsc');
        //     });
        //     // DRIVER AMBULANCE
        //     Route::group(['prefix'=>'driver'],function(){
        //         Route::get('/', [DriverPsc::class, 'main'])->name('mainDriverPsc');
        //         Route::post('/form', [DriverPsc::class, 'form'])->name('formDriverPsc');
        //         Route::post('/delete', [DriverPsc::class, 'delete'])->name('deleteDriverPsc');
        //         Route::post('/save', [DriverPsc::class, 'store'])->name('saveDriverPsc');
        //     });
        //     // SYARAT AMBULANCE
        //     Route::group(['prefix'=>'syarat-aturan'],function(){
        //         Route::get('/', [SyaratPsc::class, 'main'])->name('mainSyaratPsc');
        //         Route::post('/save', [SyaratPsc::class, 'store'])->name('saveSyaratPsc');
        //     });
        //     // PENGATURAN AMBULANCE
        //     Route::group(['prefix'=>'pengaturan'],function(){
        //         Route::get('/', [PengaturanPsc::class, 'main'])->name('mainPengaturanPsc');
        //         Route::post('/save', [PengaturanPsc::class, 'store'])->name('savePengaturanPsc');
        //     });
        // });
        #Laporan Layanan
        Route::group(['prefix'=>'laporan-layanan'],function(){
			Route::get('/', [LapLayanan::class, 'main'])->name('mainLaporanLayanan');
		});
        #Laporan Keuangan
        Route::group(['prefix'=>'laporan-keuangan'],function(){
			Route::get('/', [LapKeuangan::class, 'main'])->name('mainLaporanKeuangan');
		});
    }); 
    #Admin HOMECARE
    Route::group(['middleware' => ['cek_login:adminhomecare']], function () {
        Route::group(['prefix'=>'pendaftaran-homecare'],function(){
            #Permintaan homecare
            Route::group(['prefix'=>'permintaan'],function(){
                Route::get('/', [PermintaanHC::class, 'main'])->name('mainPermintaanHC');
                Route::post('/form-permintaan-hc', [PermintaanHC::class, 'form'])->name('formPermintaanHC');
                Route::post('/save-permintaan-hc', [PermintaanHC::class, 'save'])->name('savePermintaanHC');
                Route::post('/tolak-permintaan-hc', [PermintaanHC::class, 'tolak'])->name('tolakPermintaanHc');
                Route::post('/terima-permintaan-hc', [PermintaanHC::class, 'terima'])->name('terimaPermintaanHc');
            });
            #Riwayat homecare
            Route::group(['prefix'=>'riwayat'],function(){
                Route::get('/', [RiwayatHC::class, 'main'])->name('mainRiwayatHC');
                Route::post('/export', [RiwayatHC::class, 'export'])->name('exportRiwayatHC');
            });
            #Layanan homecare
            Route::group(['prefix'=>'layanan'],function(){
                Route::get('/', [LayananHC::class, 'main'])->name('mainLayananHC');
                Route::post('/form', [LayananHC::class, 'form'])->name('formLayananHC');
                Route::post('/delete-layanan-homecare', [LayananHC::class, 'delete'])->name('deleteLayananHC');
                Route::post('/save', [LayananHC::class, 'store'])->name('saveLayananHC');
            });
            #Paket homecare
            Route::group(['prefix'=>'paket'],function(){
                Route::get('/', [PaketHC::class, 'main'])->name('mainPaketHC');
                Route::post('/form', [PaketHC::class, 'form'])->name('formPaketHC');
                Route::post('/delete', [PaketHC::class, 'delete'])->name('deletePaketHC');
                Route::post('/save', [PaketHC::class, 'store'])->name('savePaketHC');
            });
            #Tenaga medis homecare
            Route::group(['prefix'=>'tenaga-medis'],function(){
                Route::get('/', [TenagaMedis::class, 'main'])->name('nakesHomecare');
                Route::post('/get-nakes-homecare', [TenagaMedis::class, 'getNakes'])->name('getNakesHomecare');
                Route::post('/form', [TenagaMedis::class, 'form'])->name('formNakesHomecare');
                Route::post('/delete', [TenagaMedis::class, 'delete'])->name('deleteNakesHomecare');
                Route::post('/save', [TenagaMedis::class, 'store'])->name('saveNakesHomecare');
            });
            #Pengaturan homecare
            Route::group(['prefix'=>'pengaturan'],function(){
                Route::get('/', [PengaturanHC::class, 'form'])->name('formPengaturanHC');
                Route::get('/get', [PengaturanHC::class, 'get'])->name('getPengaturanHC');
                Route::post('/save', [PengaturanHC::class, 'store'])->name('savePengaturanHC');
            });
            #Syarat homecare
            Route::group(['prefix'=>'syarat-aturan'],function(){
                Route::get('/', [SyaratHC::class, 'main'])->name('mainSyaratHC');
                Route::post('/save', [SyaratHC::class, 'store'])->name('saveSyaratHC');
            });
        });
    });
    #Admin MCU
    Route::group(['middleware' => ['cek_login:adminmcu']], function () {
        Route::group(['prefix'=>'pendaftaran-mcu'],function(){
            // PERMINTAAN MCU
            Route::group(['prefix'=>'permintaan'],function(){
                Route::get('/', [PermintaanMcu::class, 'main'])->name('mainPermintaanMcu');
                Route::post('/bayar', [PermintaanMcu::class, 'form'])->name('formBayarPermintaanMcu');
                Route::post('/simpan-bayar', [PermintaanMcu::class, 'simpan'])->name('simpanBayarPermintaanMcu');
                Route::post('/proses', [PermintaanMcu::class, 'proses'])->name('prosesPermintaanMcu');
            });
            // RIWAYAT MCU
            Route::group(['prefix'=>'riwayat'],function(){
                Route::get('/', [RiwayatMcu::class, 'main'])->name('mainRiwayatMcu');
            });
            // LAYANAN MCU
            Route::group(['prefix'=>'layanan'],function(){
                Route::get('/', [LayananMcu::class, 'main'])->name('mainLayananMcu');
                Route::post('/form', [LayananMcu::class, 'form'])->name('formLayananMcu');
                Route::post('/delete', [LayananMcu::class, 'delete'])->name('deleteLayananMcu');
                Route::post('/save', [LayananMcu::class, 'store'])->name('saveLayananMcu');
            });
            // SYARAT MCU
            Route::group(['prefix'=>'syarat-aturan'],function(){
                Route::get('/', [SyaratMcu::class, 'main'])->name('mainSyaratMcu');
                Route::post('/save', [SyaratMcu::class, 'store'])->name('saveSyaratMcu');
            });
            // PENGATURAN MCU
            Route::group(['prefix'=>'pengaturan'],function(){
                Route::get('/', [PengaturanMcu::class, 'form'])->name('formPengaturanMCU');
                Route::get('/get', [PengaturanMcu::class, 'get'])->name('getPengaturanMCU');
                Route::post('/save', [PengaturanMcu::class, 'store'])->name('savePengaturanMcu');
            });
        });
    });
    #Admin TELEMEDIS
    Route::middleware(['cek_login:admintelemedis'])->group(function () {
        # Telemedicine
        Route::group(['prefix'=>'pendaftaran-telemedicine'],function(){
            Route::group(['prefix'=>'permintaan'],function(){
                Route::get('/', [PermintaanTelemedicine::class, 'main'])->name('mainPermintaanTelemedicine');
                Route::post('/form-permintaan-telemedicine', [PermintaanTelemedicine::class, 'form'])->name('formPermintaanTelemedicine');
                Route::post('/save-permintaan-telemedicine', [PermintaanTelemedicine::class, 'save'])->name('savePermintaanTelemedicine');
                Route::post('/tolak-permintaan-telemedicine', [PermintaanTelemedicine::class, 'tolak'])->name('tolakPermintaanTelemedicine');
                Route::post('/terima-permintaan-telemedicine', [PermintaanTelemedicine::class, 'terima'])->name('terimaPermintaanTelemedicine');
            });
        }); 
        # RIWAYAT Telemedicine
        Route::group(['prefix'=>'riwayat'],function(){
            Route::get('/', [RiwayatTelemedicine::class, 'main'])->name('mainRiwayatTelemedicine');
            Route::post('/export', [RiwayatTelemedicine::class, 'export'])->name('exportRiwayatTelemedicine');
        });
        # LAYANAN Telemedicine
        Route::group(['prefix'=>'layanan'],function(){
            Route::get('/', [LayananTelemedicine::class, 'main'])->name('mainLayananTelemedicine');
            Route::post('/form', [LayananTelemedicine::class, 'form'])->name('formLayananTelemedicine');
            Route::post('/delete', [LayananTelemedicine::class, 'delete'])->name('deleteLayananTelemedicine');
            Route::post('/save', [LayananTelemedicine::class, 'store'])->name('saveLayananTelemedicine');
        });
        # TENAGA MEDIS
        Route::group(['prefix'=>'tenaga-medis'],function(){
            Route::get('/', [TenagaMedisTelemedicine::class, 'main'])->name('mainTenagaMedis');
            Route::post('/form', [TenagaMedisTelemedicine::class, 'form'])->name('formTenagaMedisTelemedicine');
            Route::post('/delete', [TenagaMedisTelemedicine::class, 'delete'])->name('deleteTenagaMedisTelemedicine');
            Route::post('/save', [TenagaMedisTelemedicine::class, 'store'])->name('saveTenagaMedisTelemedicine');
            Route::post('/get-nakes-telemedicine', [TenagaMedisTelemedicine::class, 'getNakesTelemedicine'])->name('getNakesTelemedicine');
        });
        # SYARAT Telemedicine
        Route::group(['prefix'=>'syarat-aturan'],function(){
            Route::get('/', [SyaratTelemedicine::class, 'main'])->name('mainSyaratTelemedicine');
            Route::post('/save', [SyaratTelemedicine::class, 'store'])->name('saveSyaratTelemedicine');
        });
        # PENGATURAN Telemedicine
        Route::group(['prefix'=>'pengaturan'],function(){
            Route::get('/', [PengaturanTelemedicine::class, 'form'])->name('formPengaturanTelemedicine');
            Route::get('/get', [PengaturanTelemedicine::class, 'get'])->name('getPengaturanTelemedicine');
            Route::post('/save', [PengaturanTelemedicine::class, 'store'])->name('savePengaturanTelemedicine');
        });    
    });
});
Route::group(['prefix' => 'invoice'], function() {
    Route::get('/invoice_mcu/{id}', [PermintaanMcu::class, 'invoiceMcu'])->name('invoiceMcu');
});
