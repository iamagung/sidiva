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
use App\Models\PaymentPermintaan;
use App\Models\DBRANAP\Users as UserRanap;
use App\Models\DBRSUD\TmPoli as PoliRsu;
use DataTables, DB, Auth;

class LaporanKeuanganController extends Controller
{
    function __construct() {
		$this->title = 'Laporan Keuangan';
	}

    public function main(Request $request) {
        $data['title'] = $this->title;
        return view('admin.laporan.lap-keuangan', $data);
    }
    #Datatable homecare
    public function datatableHomecareKeuangan(Request $request) {
        if ($request->ajax()) {
            $awal = date('Y-m-d', strtotime("01-".$request->awal));
            $akhir = date('Y-m-d', strtotime("31-".$request->akhir));
            if (!empty($request->awal)&&!empty(!empty($request->akhir))) {
                $data = PaymentPermintaan::select(
                        'payment_permintaan.permintaan_id',
                        'payment_permintaan.nominal',
                        'payment_permintaan.jenis_layanan',
                        'payment_permintaan.status',
                        'payment_permintaan.ongkos_kirim',
                        'pc.id_permintaan_hc',
                        'pc.no_rm',
                        'pc.nama',
                        'pc.alamat',
                        'pc.tanggal_order',
                        'pc.tanggal_kunjungan'
                    )
                    ->leftJoin('permintaan_hc as pc','pc.id_permintaan_hc','payment_permintaan.permintaan_id')
                    ->whereBetween('pc.tanggal_kunjungan', [$awal, $akhir])
                    ->where('payment_permintaan.jenis_layanan','homecare')
                    ->orderBy('pc.id_permintaan_hc','DESC')->get();
            } else {
                $data = PaymentPermintaan::select(
                    'payment_permintaan.permintaan_id',
                    'payment_permintaan.nominal',
                    'payment_permintaan.jenis_layanan',
                    'payment_permintaan.status',
                    'payment_permintaan.ongkos_kirim',
                    'pc.id_permintaan_hc',
                    'pc.no_rm',
                    'pc.nama',
                    'pc.alamat',
                    'pc.tanggal_order',
                    'pc.tanggal_kunjungan'
                )
                ->leftJoin('permintaan_hc as pc','pc.id_permintaan_hc','payment_permintaan.permintaan_id')
                ->where('payment_permintaan.jenis_layanan','homecare')
                ->orderBy('pc.id_permintaan_hc','DESC')->get();
            }
            foreach ($data as $k => $v) {
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
                if (in_array($row->status,['PAID','SETTLED'])) {
                    $text = 'TERBAYARKAN';
                } elseif (in_array($row->status,['UNCONFIRMED','PENDING'])) {
                    $text = 'BELUM BAYAR';
                } else {
                    $text = 'EXPIRED';
                }
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
            ->addColumn('modfiyTagihan', function($row){
                $text = "Rp. " . number_format($row->nominal, 2, ",", ".");
                return $text;
            })
            ->addColumn('modifyTerbayarkan', function($row){
                if (in_array($row->status,['SETTLED','PAID'])) {
                    $text = "Rp. " . number_format($row->nominal, 2, ",", ".");
                } else {
                    $text = "Rp. " . number_format("0", 2, ",", ".");
                }
                return $text;
            })
            ->toJson();
		}
        return view('admin.laporan.lap-keuangan', $data);
    }
    #Datatable Telemedicine
    public function datatableTelemedicineKeuangan(Request $request) {
        if ($request->ajax()) {
            $awal = date('Y-m-d', strtotime("01-".$request->awal));
            $akhir = date('Y-m-d', strtotime("31-".$request->akhir));
            if (!empty($request->awal)&&!empty(!empty($request->akhir))) {
                $data = PaymentPermintaan::select(
                        'payment_permintaan.permintaan_id',
                        'payment_permintaan.nominal',
                        'payment_permintaan.jenis_layanan',
                        'payment_permintaan.status',
                        'payment_permintaan.ongkos_kirim',
                        'pt.id_permintaan_telemedicine',
                        'pt.no_rm',
                        'pt.nama',
                        'pt.alamat',
                        'pt.tanggal_order',
                        'pt.tanggal_kunjungan',
                        'pt.tenaga_medis_id',
                        'pt.perawat_id',
                        'pt.poli_id',
                        'ro.diantar'
                    )
                    ->leftJoin('permintaan_telemedicine as pt','pt.id_permintaan_telemedicine','payment_permintaan.permintaan_id')
                    ->leftJoin('resep_obat as ro','ro.permintaan_id','payment_permintaan.permintaan_id')
                    ->whereBetween('pt.tanggal_kunjungan', [$awal, $akhir])
                    ->whereIn('payment_permintaan.jenis_layanan',['telemedicine','eresep_telemedicine'])
                    ->where('ro.jenis_layanan','telemedicine')
                    ->orderBy('pt.id_permintaan_telemedicine','DESC')->get();
            } else {
                $data = PaymentPermintaan::select(
                    'payment_permintaan.permintaan_id',
                    'payment_permintaan.nominal',
                    'payment_permintaan.jenis_layanan',
                    'payment_permintaan.status',
                    'payment_permintaan.ongkos_kirim',
                    'pt.id_permintaan_telemedicine',
                    'pt.no_rm',
                    'pt.nama',
                    'pt.alamat',
                    'pt.tanggal_order',
                    'pt.tanggal_kunjungan',
                    'pt.tenaga_medis_id',
                    'pt.perawat_id',
                    'pt.poli_id',
                    'ro.diantar'
                )
                ->leftJoin('permintaan_telemedicine as pt','pt.id_permintaan_telemedicine','payment_permintaan.permintaan_id')
                ->leftJoin('resep_obat as ro','ro.permintaan_id','payment_permintaan.permintaan_id')
                ->whereIn('payment_permintaan.jenis_layanan',['telemedicine','eresep_telemedicine'])
                ->where('ro.jenis_layanan','telemedicine')
                ->orderBy('pt.id_permintaan_telemedicine','DESC')->get();
            }
            // return$data;
            // return$telemedicine = $data->where('diantar','ya')->whereIn('status',['PAID','SETTLED']);
            // $subtotal = 0;
            // foreach ($telemedicine as $te) {
            //     $sum = (int)$te->nominal+(int)$te->ongkos_kirim;
            //     $subtotal += $sum;
            // }
            // return $subtotal;
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('modifyStatus', function($row){
                if (in_array($row->status,['PAID','SETTLED'])) {
                    $text = 'TERBAYARKAN';
                } elseif (in_array($row->status,['UNCONFIRMED','PENDING'])) {
                    $text = 'BELUM BAYAR';
                } else {
                    $text = 'EXPIRED';
                }
                return $text;
            })
            ->addColumn('modifyNorm', function($row){
                $text = ($row->no_rm)?$row->no_rm:'-';
                return $text;
            })
            ->addColumn('modifyDokter', function($row){
                if (!$row->tenaga_medis_id) {
                    $txt = '-';
                } else {
                    $txt = UserRanap::where('id',$row->tenaga_medis_id)->first()->name;
                }
                return $txt;
            })
            ->addColumn('modifyPerawat', function($row){
                if (!$row->perawat_id) {
                    $txt = '-';
                } else {
                    $txt = UserRanap::where('id',$row->perawat_id)->first()->name;
                }
                return $txt;
            })
            ->addColumn('modifyPoli', function($row){
                if (!$row->poli_id) {
                    $txt = '-';
                } else {
                    $txt = PoliRsu::where('KodePoli',$row->poli_id)->first()->NamaPoli;
                }
                return $txt;
            })
            ->addColumn('modifyTagihan', function($row){
                $tagihan = $row->nominal+$row->ongkos_kirim;
                $text = "Rp. " . number_format($tagihan, 2, ",", ".");
                return $text;
            })
            ->addColumn('modifyTerbayarkan', function($row){
                $tagihan = $row->nominal+$row->ongkos_kirim;
                if (in_array($row->status,['SETTLED','PAID'])) {
                    $text = "Rp. " . number_format($tagihan, 2, ",", ".");
                } else {
                    $text = "Rp. " . number_format("0", 2, ",", ".");
                }
                return $text;
            })
            // ->addColumn('tes', function($row){
            //     return "Rp. 1.000.000,00";
            // })
            // ->with('test', )
            ->toJson();
		}
        return view('admin.laporan.lap-keuangan', $data);
    }
    #Datatable MCU
    public function datatableMcuKeuangan(Request $request) {
        if ($request->ajax()) {
            $awal = date('Y-m-d', strtotime("01-".$request->awal));
            $akhir = date('Y-m-d', strtotime("31-".$request->akhir));
            if (!empty($request->awal)&&!empty(!empty($request->akhir))) {
                $data = PaymentPermintaan::select(
                        'payment_permintaan.permintaan_id',
                        'payment_permintaan.nominal',
                        'payment_permintaan.jenis_layanan',
                        'payment_permintaan.status',
                        'payment_permintaan.ongkos_kirim',
                        'pu.id_permintaan',
                        'pu.no_rm',
                        'pu.nama',
                        'pu.alamat',
                        'pu.tanggal_order',
                        'pu.tanggal_kunjungan',
                        'pu.jenis_mcu'
                    )
                    ->leftJoin('permintaan_mcu as pu','pu.id_permintaan','payment_permintaan.permintaan_id')
                    ->whereBetween('pu.tanggal_kunjungan', [$awal, $akhir])
                    ->where('payment_permintaan.jenis_layanan','mcu')
                    ->orderBy('pu.id_permintaan','DESC')->get();
            } else {
                $data = PaymentPermintaan::select(
                    'payment_permintaan.permintaan_id',
                    'payment_permintaan.nominal',
                    'payment_permintaan.jenis_layanan',
                    'payment_permintaan.status',
                    'payment_permintaan.ongkos_kirim',
                    'pu.id_permintaan',
                    'pu.no_rm',
                    'pu.nama',
                    'pu.alamat',
                    'pu.tanggal_order',
                    'pu.tanggal_kunjungan',
                    'pu.jenis_mcu'
                )
                ->leftJoin('permintaan_mcu as pu','pu.id_permintaan','payment_permintaan.permintaan_id')
                ->where('payment_permintaan.jenis_layanan','mcu')
                ->orderBy('pu.id_permintaan','DESC')->get();
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
                if (in_array($row->status,['PAID','SETTLED'])) {
                    $text = 'TERBAYARKAN';
                } elseif (in_array($row->status,['UNCONFIRMED','PENDING'])) {
                    $text = 'BELUM';
                } else {
                    $text = 'EXPIRED';
                }
                return $text;
            })
            ->addColumn('modifyNorm', function($row){
                $text = ($row->no_rm)?$row->no_rm:'-';
                return $text;
            })
            ->addColumn('modifyJenis', function($row){
                $text = strtoupper($row->jenis_mcu);
                return $text;
            })
            ->addColumn('modifyLayanan', function($row){
                $txt = !empty($row->listLayanan)?$row->listLayanan:'-';
                return $txt;
            })
            ->addColumn('modfiyTagihan', function($row){
                $text = "Rp. " . number_format($row->nominal, 2, ",", ".");
                return $text;
            })
            ->addColumn('modifyTerbayarkan', function($row){
                if (in_array($row->status,['SETTLED','PAID'])) {
                    $text = "Rp. " . number_format($row->nominal, 2, ",", ".");
                } else {
                    $text = "Rp. " . number_format("0", 2, ",", ".");
                }
                return $text;
            })
            ->toJson();
		}
        return view('admin.laporan.lap-keuangan', $data);
    }
}
