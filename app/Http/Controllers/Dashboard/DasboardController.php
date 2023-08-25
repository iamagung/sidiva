<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanHC;
use App\Models\PermintaanMcu;
use Carbon\Carbon;

class DasboardController extends Controller
{
    function __construct()
	{
		$this->title = 'Dashboard';
        $this->menu = 'dashboard';
	}

    public function main()
    {
        $data['title'] = $this->title;
        $data['menu'] = $this->menu;
        return view('admin.dashboard.main', $data);
    }

    public function getData()
    {
        $today = date('Y-m-d');
        $monthNow = date('m');
        $yearNow = date('Y');
        $monthPrev = now()->subMonth()->format('m');
        $yearPrev = now()->subMonth()->format('Y');
        $data['ttlPermintaanHC']   = PermintaanHC::where('tanggal_kunjungan', $today)->count();
        $data['ttlTerlayananiHC']  = PermintaanHC::whereNotIn('status_pasien', ['belum', 'batal'])->where('tanggal_kunjungan', $today)->count();
        $data['ttlPermintaanMCU']  = PermintaanMcu::where('tanggal_kunjungan', $today)->count();
        $data['ttlTerlayananiMCU'] = PermintaanMcu::whereNotIn('status_pasien', ['belum', 'batal'])->where('tanggal_kunjungan', $today)->count();
        #1 Start calculate presentase permintaan HC bulan ini dengan bulan kemaren
        $getHcNowMonth  = PermintaanHC::whereYear('tanggal_kunjungan', '=', $yearNow)->whereMonth('tanggal_kunjungan', '=', $monthNow)->count();
        $getHcPrevMonth  = PermintaanHC::whereYear('tanggal_kunjungan', '=', $yearPrev)->whereMonth('tanggal_kunjungan', '=', $monthPrev)->count();
        if ($getHcNowMonth > 0 && $getHcPrevMonth > 0) {
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
        #2 Start calculate presentase Homecare Terlayani bulan ini dengan bulan kemaren
        $getLayanHcNowMonth  = PermintaanHC::whereNotIn('status_pasien', ['belum', 'batal'])->whereYear('tanggal_kunjungan', '=', $yearNow)->whereMonth('tanggal_kunjungan', '=', $monthNow)->count();
        $getLayanHcPrevMonth  = PermintaanHC::whereNotIn('status_pasien', ['belum', 'batal'])->whereYear('tanggal_kunjungan', '=', $yearPrev)->whereMonth('tanggal_kunjungan', '=', $monthPrev)->count();
        if ($getLayanHcNowMonth > 0 && $getLayanHcPrevMonth > 0) {
            $percentLayanHC = round((($getLayanHcNowMonth-$getLayanHcPrevMonth)/$getLayanHcNowMonth)*100);
            if (substr($percentLayanHC, 0,1) == '-' || $percentLayanHC == 0) {
                $data['diffLayanHC'] = "$percentLayanHC%";
            } else {
                $data['diffLayanHC'] = "+$percentLayanHC%";
            }
        } else {
            $data['diffLayanHC'] = "+0%";
        }
        # end
        #3 Start calculate presentase permintaan MCU bulan ini dengan bulan kemaren
        $getMcuNowMonth  = PermintaanMcu::whereYear('tanggal_kunjungan', '=', $yearNow)->whereMonth('tanggal_kunjungan', '=', $monthNow)->count();
        $getMcuPrevMonth  = permintaanMcu::whereYear('tanggal_kunjungan', '=', $yearPrev)->whereMonth('tanggal_kunjungan', '=', $monthPrev)->count();
        if ($getMcuNowMonth > 0 && $getMcuPrevMonth > 0) {
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
        #4 Start calculate presentase MCU Terlayani bulan ini dengan bulan kemaren
        $getLayanMcuNowMonth  = PermintaanMcu::whereNotIn('status_pasien', ['belum', 'batal'])->whereYear('tanggal_kunjungan', '=', $yearNow)->whereMonth('tanggal_kunjungan', '=', $monthNow)->count();
        $getLayanMcuPrevMonth  = PermintaanMcu::whereNotIn('status_pasien', ['belum', 'batal'])->whereYear('tanggal_kunjungan', '=', $yearPrev)->whereMonth('tanggal_kunjungan', '=', $monthPrev)->count();
        if ($getLayanMcuNowMonth > 0 && $getLayanMcuPrevMonth > 0) {
            $percentLayanMcu = round((($getLayanMcuNowMonth-$getLayanMcuPrevMonth)/$getLayanMcuNowMonth)*100);
            if (substr($percentLayanMcu, 0,1) == '-' || $percentLayanMcu == 0) {
                $data['diffLayanMcu'] = "$percentLayanMcu%";
            } else {
                $data['diffLayanMcu'] = "+$percentLayanMcu%";
            }
        } else {
            $data['diffLayanMcu'] = "+0%";
        }
        # end
        return ['code' => 200, 'status' => 'success', 'message' => 'Berhasil', 'data' => $data];
    }       
}