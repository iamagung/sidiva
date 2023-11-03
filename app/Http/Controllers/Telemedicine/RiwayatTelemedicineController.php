<?php

namespace App\Http\Controllers\Telemedicine;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanTelemedicine;
use App\Models\PaketTelemedicine;
use App\Models\LayananTelemedicine;
use App\Helpers\Helpers as Help;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Facades\Excel;
use DataTables, Validator, DB, Auth;

class RiwayatTelemedicineController extends Controller
{
    function __construct()
	{
		$this->title = 'Riwayat Telemedicine';
	}

    public function main(Request $request)
    {
        if(request()->ajax()){
            $data = PermintaanTelemedicine::select('poli_id', 'permintaan_telemedicine.tanggal_order', 'permintaan_telemedicine.no_rm', 'permintaan_telemedicine.nama', 'tenaga_medis_id', 'perawat_id', 'tanggal_kunjungan', 'status_pasien')
                    ->with('tmPoli:NamaPoli,KodePoli')
                    ->with(['dokter' => function($q) {
                        $q->select('nakes_id')->with('user_ranap:id,name as nama_dokter');
                    }])
                    ->with(['perawat' => function($q) {
                        $q->select('nakes_id')->with('user_ranap:id,name as nama_perawat');
                    }])
                    ->when($request->status!='all',fn($q) => 
                        $q->where('status_pasien', $request->status)
                    )
                    ->whereBetween('tanggal_order', [$request->min, $request->max])
                    ->orderBy('permintaan_telemedicine.created_at','ASC')->get();
			return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function($row){
                if($row->status_pasien != 'batal' && $row->status_pasien != 'selesai' && $row->status_pasien != 'tolak') {
                    $text = "<a href='javascript:void(0)' class='btn btn-sm btn-primary' onclick='lanjutkan(`$row->id_permintaan_hc`)'>LANJUTKAN</a>";
                } else {
                    $text = "<span>".strtoupper($row->status_pasien)."</span>";
                }
                return $text;
            })
            ->addColumn('no_rm', function($row){
                if (!empty($row->no_rm)) {
                    $text = $row->no_rm;
                } else {
                    $text = '-';
                }
                return $text;
            })
            ->addColumn('nama_poli', function($row){
                if (!empty($row->tmPoli)) {
                    $text = $row->tmPoli->NamaPoli;
                } else {
                    $text = '-';
                }
                return $text;
            })
            ->addColumn('nama_dokter', function($row){
                if (!empty($row->dokter)) {
                    if (!empty($row->dokter->user_ranap)) {
                        $text = $row->dokter->user_ranap->nama_dokter;
                    } else {
                        $text = '-';
                    }
                } else {
                    $text = '-';
                }
                return $text;
            })
            ->addColumn('nama_perawat', function($row){
                if (!empty($row->perawat)) {
                    if (!empty($row->perawat->user_ranap)) {
                        $text = $row->perawat->user_ranap->nama_perawat;
                    } else {
                        $text = '-';
                    }
                } else {
                    $text = '-';
                }
                return $text;
            })
            ->rawColumns(['status'])
            ->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.telemedicine.riwayat.main', $data);
    }

    public function export(Request $request)
	{
		$data['date'] = date('Y-m-d');
		$data['judul'] = 'Telemedicine';
        $data['periode'] = $request->startDate.' - '.$request->endDate;
        $data['status'] = ($request->status == 'all') ? 'Semua' : $request->status;
		$startDate = $request->startDate;
		$endDate = $request->endDate;
		$this->query($startDate, $endDate, $request->status);
		$data['data'] = $this->data;
		if (count($this->data) > 0) {
			$content = view('admin.telemedicine.riwayat.excel', $data)->render();
			return ['status' => 'success', 'content' => $content];
		} else {
			return ['status' => 'error', 'message' => 'Data tidak ditemukan pada tanggal tersebut!'];
		}
	}

    public function query($startDate, $endDate, $status)
	{
		// Pencarian between two date
		if(!empty($startDate) && !empty($endDate)) {
			if($status == 'all') {
                $data = PermintaanTelemedicine::select('tm_poli.NamaPoli as nama_poli', 'permintaan_telemedicine.tanggal_order', 'permintaan_telemedicine.no_rm', 'permintaan_telemedicine.nik', 'permintaan_telemedicine.alamat', 'permintaan_telemedicine.jenis_kelamin', 'permintaan_telemedicine.tanggal_lahir', 'no_telepon', 'keluhan', 'jadwal_dokter', 'biaya_layanan', 'permintaan_telemedicine.nama', 'dokter.name as nama_dokter', 'perawat.name as nama_perawat', 'tanggal_kunjungan', 'status_pasien')
                    ->join(DB::connection('dbrsud')->raw('dbsimars_baru.tm_poli'), 'permintaan_telemedicine.poli_id', '=', 'tm_poli.KodePoli')
                    ->join(DB::connection('dbranap')->raw('wahidin_ranap.users as dokter'), 'permintaan_telemedicine.tenaga_medis_id', '=', 'dokter.id')
                    ->leftJoin(DB::connection('dbranap')->raw('wahidin_ranap.users as perawat'), 'permintaan_telemedicine.perawat_id', '=', 'perawat.id')
                    ->whereBetween('tanggal_order', [$startDate,$endDate])
                    ->orderBy('permintaan_telemedicine.created_at','ASC')->get();
            } else {
                $data = PermintaanTelemedicine::select('tm_poli.NamaPoli as nama_poli', 'permintaan_telemedicine.tanggal_order', 'permintaan_telemedicine.no_rm', 'permintaan_telemedicine.nik', 'permintaan_telemedicine.alamat', 'permintaan_telemedicine.jenis_kelamin', 'permintaan_telemedicine.tanggal_lahir', 'no_telepon', 'keluhan', 'jadwal_dokter', 'biaya_layanan', 'permintaan_telemedicine.nama', 'dokter.name as nama_dokter', 'perawat.name as nama_perawat', 'tanggal_kunjungan', 'status_pasien')
                    ->join(DB::connection('dbrsud')->raw('dbsimars_baru.tm_poli'), 'permintaan_telemedicine.poli_id', '=', 'tm_poli.KodePoli')
                    ->join(DB::connection('dbranap')->raw('wahidin_ranap.users as dokter'), 'permintaan_telemedicine.tenaga_medis_id', '=', 'dokter.id')
                    ->leftJoin(DB::connection('dbranap')->raw('wahidin_ranap.users as perawat'), 'permintaan_telemedicine.perawat_id', '=', 'perawat.id')
                    ->whereBetween('tanggal_order', [$startDate,$endDate])
                    ->where('status_pasien', $status)
                    ->orderBy('permintaan_telemedicine.created_at','ASC')->get();
            }
        }
		$this->data = $data;
	}
}
