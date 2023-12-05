<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LayananHC;
use App\Models\LayananMcu;
use App\Models\PermintaanHC;
use App\Models\PermintaanMcu;
use App\Models\PermintaanTelemedicine;
use App\Models\LayananPermintaanHc;
use App\Models\LayananPermintaanMcu;
use App\Exports\LaporanLayananHomecareExport;
use App\Exports\LaporanLayananTelemedicineExport;
use App\Exports\LaporanLayananMcuExport;
use DataTables, DB, Auth, Excel;

class LaporanLayananController extends Controller
{
    function __construct() {
		$this->title = 'Laporan Layanan';
	}

    public function main(Request $request) {
        $data['title'] = $this->title;
        return view('admin.laporan.lap-layanan', $data);
    }

    public function exportLaporanLayanan(Request $request) {
        $bulan = substr($request->bulan,0,2);
        $tahun = substr($request->bulan,3,7);

        if ($request->jenis == 'homecare') {
            return Excel::download(new LaporanLayananHomecareExport($bulan, $tahun), "Laporan Keuangan Homecare $bulan - $tahun.xlsx");
        } else if ($request->jenis == 'telemedicine') {
            return Excel::download(new LaporanLayananTelemedicineExport($bulan, $tahun), "Laporan Keuangan Telemedicine $bulan - $tahun.xlsx");
        } else if ($request->jenis == 'mcu') {
            return Excel::download(new LaporanLayananMcuExport($bulan, $tahun), "Laporan Keuangan Medical Check Up $bulan - $tahun.xlsx");            
        } else {
            return Excel::download(new LaporanLayananTelemedicineExport($bulan, $tahun), "Laporan Keuangan Homecare $bulan - $tahun.xlsx");
        }
    }

    #Datatable homecare
    public function datatableHomecare(Request $request) {
        if ($request->ajax()) {
            // return$month = date('m', strtotime($request->bulan));
            $bulan = substr($request->bulan,0,2);
            $tahun = substr($request->bulan,3,7);
            if (!empty($request->bulan)) {
                $data = PermintaanHC::whereMonth('tanggal_kunjungan', $bulan)
                    ->whereYear('tanggal_kunjungan', $tahun)
                    ->orderBy('id_permintaan_hc','DESC')->get();
            } else {
                $data = PermintaanHC::orderBy('id_permintaan_hc','DESC')->get();
            }
            foreach ($data as $k => $v) {
                // return $v;
                $layanan = LayananPermintaanHc::where('permintaan_id', $v->id_permintaan_hc)->get();
                foreach ($layanan as $key => $val) {
                    $namaLayanan = LayananHC::where('id_layanan_hc', $val->layanan_id)->first();
                    if (!empty($namaLayanan)) {
                        if(!isset($v->listLayanan)){
                            $v->listLayanan = $namaLayanan->nama_layanan;
                        }else{
                            $v->listLayanan .= ", " . $namaLayanan->nama_layanan;
                        }
                    }
                }
            }
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('modifyStatus', function($row){
                $text = strtoupper($row->status_pasien);
                return $text;
            })
            ->addColumn('modifyNorm', function($row){
                $text = ($row->no_rm)?$row->no_rm:'-';
                return $text;
            })
            ->addColumn('modifyLayanan', function($row){
                $txt = !empty($row->listLayanan)?$row->listLayanan:'-';
                return $txt;
            })
            ->rawColumns(['actions'])
            ->toJson();
		}
        return view('admin.laporan.lap-layanan', $data);
    }
    #Datatable Telemedicine
    public function datatableTelemedicine(Request $request) {
        if ($request->ajax()) {
            // return$month = date('m', strtotime($request->bulan));
            $bulan = substr($request->bulan,0,2);
            $tahun = substr($request->bulan,3,7);
            if (!empty($request->bulan)) {
                $data = PermintaanTelemedicine::select(
                        'id_permintaan_telemedicine',
                        'tanggal_order',
                        'no_rm',
                        'nama',
                        'permintaan_telemedicine.no_telepon',
                        'poli_id',
                        'tenaga_medis_id',
                        'tanggal_kunjungan',
                        'jadwal_dokter',
                        'status_pembayaran',
                        'status_pasien',
                        'perawat_id'
                    )
                    ->with('tmPoli:KodePoli,NamaPoli')
                    ->with('nakes:id,name')
                    ->with('nakes_perawat:id,name')
                    ->whereMonth('tanggal_kunjungan', $bulan)
                    ->whereYear('tanggal_kunjungan', $tahun)
                    ->orderBy('id_permintaan_telemedicine','DESC')->get();
            } else {
                $data = PermintaanTelemedicine::select(
                    'id_permintaan_telemedicine',
                    'tanggal_order',
                    'no_rm',
                    'nama',
                    'permintaan_telemedicine.no_telepon',
                    'poli_id',
                    'tenaga_medis_id',
                    'tanggal_kunjungan',
                    'jadwal_dokter',
                    'status_pembayaran',
                    'status_pasien',
                    'perawat_id'
                )
                ->with('tmPoli:KodePoli,NamaPoli')
                ->with('nakes:id,name')
                ->with('nakes_perawat:id,name')
                ->orderBy('id_permintaan_telemedicine','DESC')->get();
            }
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('modifyStatus', function($row){
                $text = strtoupper($row->status_pasien);
                return $text;
            })
            ->addColumn('modifyNorm', function($row){
                $text = ($row->no_rm)?$row->no_rm:'-';
                return $text;
            })
            ->addColumn('modifyDokter', function($row){
                // return $row;
                if (!$row->nakes) {
                    $txt = "<text class='text-center'>-</text>";
                } else {
                    $txt = "<text class='text-center'>".$row->nakes->name."</text>";
                }
                return $txt;
            })
            ->addColumn('modifyPerawat', function($row){
                if ($row->nakes_perawat == null) {
                    $txt = "<text class='text-center'>-</text>";
                } else {
                    $txt = "<text class='text-center'>".$row->nakes_perawat->name."</text>";
                }
                return $txt;
            })
            ->addColumn('modifyPoli', function($row){
                if ($row->tmPoli == null) {
                    $txt = "<text class='text-danger text-center'>-</text>";
                } else {
                    $txt = "<text class=' text-center'>".$row->tmPoli->NamaPoli."</text>";
                }
                return $txt;
            })
            ->rawColumns(['actions','modifyDokter','modifyNorm','modifyPoli','modifyPerawat'])
            ->toJson();
		}
        return view('admin.laporan.lap-layanan', $data);
    }
    #Datatable MCU
    public function datatableMCU(Request $request) {
        if ($request->ajax()) {
            $bulan = substr($request->bulan,0,2);
            $tahun = substr($request->bulan,3,7);
            if (!empty($request->bulan)) {
                $data = PermintaanMcu::whereMonth('tanggal_kunjungan', $bulan)
                    ->whereYear('tanggal_kunjungan', $tahun)
                    ->orderBy('id_permintaan','DESC')->get();
            } else {
                $data = PermintaanMcu::orderBy('id_permintaan','DESC')->get();
            }
            foreach ($data as $k => $v) {
                $layanan = LayananPermintaanMcu::where('permintaan_id', $v->id_permintaan)->get();
                foreach ($layanan as $key => $val) {
                    $namaLayanan = LayananMcu::where('id_layanan', $val->layanan_id)->first();
                    if (!empty($namaLayanan)) {
                        if(!isset($v->listLayanan)){
                            $v->listLayanan = $namaLayanan->nama_layanan;
                        }else{
                            $v->listLayanan .= ", " . $namaLayanan->nama_layanan;
                        }
                    }
                }
            }
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('modifyStatus', function($row){
                $text = strtoupper($row->status_pasien);
                return $text;
            })
            ->addColumn('modifyJenis', function($row){
                $text = strtoupper($row->jenis_mcu);
                return $text;
            })
            ->addColumn('modifyNorm', function($row){
                $text = ($row->no_rm)?$row->no_rm:'-';
                return $text;
            })
            ->addColumn('modifyLayanan', function($row){
                $txt = !empty($row->listLayanan)?$row->listLayanan:'-';
                return $txt;
            })
            ->rawColumns(['actions'])
            ->toJson();
		}
        return view('admin.laporan.lap-layanan', $data);
    }
    #Datatable Emergency
    public function datatableEmergency(Request $request) {
        if ($request->ajax()) {
            // return$month = date('m', strtotime($request->bulan));
            $bulan = substr($request->bulan,0,2);
            $tahun = substr($request->bulan,3,7);
            if (!empty($request->bulan)) {
                $data = PermintaanHC::whereMonth('tanggal_kunjungan', $bulan)
                    ->whereYear('tanggal_kunjungan', $tahun)
                    ->orderBy('created_at','DESC')->get();
            } else {
                $data = PermintaanHC::orderBy('created_at','DESC')->get();
            }
            return DataTables::of($data)
            ->addIndexColumn()
            ->rawColumns(['actions'])
            ->toJson();
		}
        return view('admin.laporan.lap-layanan', $data);
    }
}
