<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanHC;
use App\Models\PermintaanTelemedicine;
use App\Models\PermintaanMcu;
use Carbon\Carbon;

class DasboardController extends Controller
{
    # Start admin
    public function main() {
        $data['title'] = 'Dashboard';
        $data['menu'] = 'dashboard';
        return view('admin.dashboard.main', $data);
    }
    public function getData() {
        $today = date('Y-m-d');
        $monthNow = date('m');
        $yearNow = date('Y');
        $monthPrev = now()->subMonth()->format('m');
        $yearPrev = now()->subMonth()->format('Y');
        $data['ttlPermintaanHC']   = PermintaanHC::where('tanggal_kunjungan', $today)->whereNotIn('status_pasien',['batal','tolak'])->count(); 
        $data['ttlPermintaanMCU']  = PermintaanMcu::where('tanggal_kunjungan', $today)->whereNotIn('status_pasien',['batal','tolak'])->count();
        $data['ttlPermintaanTele']  = PermintaanTelemedicine::where('tanggal_kunjungan', $today)->whereNotIn('status_pasien',['batal','tolak'])->count();
        #1 Start calculate presentase permintaan HC bulan ini dengan bulan kemaren
        $getHcNowMonth  = PermintaanHC::whereNotIn('status_pasien',['batal','tolak'])->whereYear('tanggal_kunjungan', '=', $yearNow)->whereMonth('tanggal_kunjungan', '=', $monthNow)->count();
        $getHcPrevMonth  = PermintaanHC::whereNotIn('status_pasien',['batal','tolak'])->whereYear('tanggal_kunjungan', '=', $yearPrev)->whereMonth('tanggal_kunjungan', '=', $monthPrev)->count();
        if ($getHcNowMonth>0 && $getHcPrevMonth>0) {
            $percentPermintaanHC = round((($getHcNowMonth-$getHcPrevMonth)/$getHcNowMonth)*100);
            if (substr($percentPermintaanHC, 0,1) == '-' || $percentPermintaanHC == 0) {
                $data['diffPermintaanHC'] = "$percentPermintaanHC%";
            } else {
                $data['diffPermintaanHC'] = "+$percentPermintaanHC%";
            }
        } else {
            $data['diffPermintaanHC'] = "+0%";
        }
        # end
        #2 Start calculate presentase permintaan MCU bulan ini dengan bulan kemaren
        $getMcuNowMonth  = PermintaanMcu::whereNotIn('status_pasien',['batal','tolak'])->whereYear('tanggal_kunjungan', '=', $yearNow)->whereMonth('tanggal_kunjungan', '=', $monthNow)->count();
        $getMcuPrevMonth  = permintaanMcu::whereNotIn('status_pasien',['batal','tolak'])->whereYear('tanggal_kunjungan', '=', $yearPrev)->whereMonth('tanggal_kunjungan', '=', $monthPrev)->count();
        if ($getMcuNowMonth>0 && $getMcuPrevMonth>0) {
            $percentPermintaanMCU = round((($getMcuNowMonth-$getMcuPrevMonth)/$getMcuNowMonth)*100);
            if (substr($percentPermintaanMCU, 0,1) == '-' || $percentPermintaanMCU == 0) {
                $data['diffPermintaanMcu'] = "$percentPermintaanMCU%";
            } else {
                $data['diffPermintaanMcu'] = "+$percentPermintaanMCU%";
            }
        } else {
            $data['diffPermintaanMcu'] = "+0%";
        }
        # end
        #3 Start calculate presentase permintaan Telemedicine bulan ini dengan bulan kemaren
        $getTeleNowMonth  = PermintaanMcu::whereNotIn('status_pasien',['batal','tolak'])->whereYear('tanggal_kunjungan', '=', $yearNow)->whereMonth('tanggal_kunjungan', '=', $monthNow)->count();
        $getTelePrevMonth  = permintaanMcu::whereNotIn('status_pasien',['batal','tolak'])->whereYear('tanggal_kunjungan', '=', $yearPrev)->whereMonth('tanggal_kunjungan', '=', $monthPrev)->count();
        if ($getTeleNowMonth>0 && $getTelePrevMonth>0) {
            $percentPermintaanTele = round((($getTeleNowMonth-$getTelePrevMonth)/$getTeleNowMonth)*100);
            if (substr($percentPermintaanTele, 0,1) == '-' || $percentPermintaanTele == 0) {
                $data['diffPermintaanTele'] = "$percentPermintaanTele%";
            } else {
                $data['diffPermintaanTele'] = "+$percentPermintaanTele%";
            }
        } else {
            $data['diffPermintaanTele'] = "+0%";
        }
        # end
        return ['code' => 200, 'status' => 'success', 'message' => 'Berhasil', 'data' => $data];
    }
    # End admin
}