<?php

namespace App\Http\Controllers\HC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanHC;
use App\Models\PaketHC;
use App\Models\LayananHC;
use App\Helpers\Helpers as Help;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Facades\Excel;
use DataTables, Validator, DB, Auth;

class RiwayatHCController extends Controller
{
    function __construct()
	{
		$this->title = 'Riwayat Home Care';
	}

    public function main(Request $request)
    {
        if(request()->ajax()){
            $data = PermintaanHC::where('status_pasien', 'selesai')
                ->whereBetween('tanggal_kunjungan', [$request->min, $request->max])
                ->orderBy('created_at','ASC')->get();
			return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function($row){
                if ($row->status_pasien == 'selesai') {
                    $text = 'SELESAI';
                } else if($row->status_pasien != 'batal' && $row->status_pasien != 'selesai') {
                    $text = "<a href='javascript:void(0)' style='color: #000' onclick='lanjutkan(`$row->id_permintaan_hc`)'>DILANJUTKAN</a>";
                }
                return $text;
            })
            ->addColumn('rm', function($row){
                if (!empty($row->no_rm)) {
                    $text = $row->no_rm;
                } else {
                    $text = '-';
                }
                return $text;
            })
            ->addColumn('alergi', function($row){
                if (!empty($row->alergi_pasien)) {
                    $text = $row->alergi_pasien;
                } else {
                    $text = '-';
                }
                return $text;
            })
            ->rawColumns(['actions'])
            ->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.homecare.riwayat.main', $data);
    }

    public function export(Request $request)
	{
		$data['date'] = date('Y-m-d');
		$data['judul'] = 'HOME CARE';
        $data['periode'] = $request->startDate.' - '.$request->endDate;
		$startDate = $request->startDate;
		$endDate = $request->endDate;
		$this->query($startDate, $endDate);
		$data['data'] = $this->data;
		if (count($this->data) > 0) {
			$content = view('admin.homecare.riwayat.excel', $data)->render();
			return ['status' => 'success', 'content' => $content];
		} else {
			return ['status' => 'error', 'message' => 'Data tidak ditemukan pada tanggal tersebut!'];
		}
	}

    public function query($startDate, $endDate)
	{
		// Pencarian between two date
		if(!empty($startDate) && !empty($endDate)) {
			$data = PermintaanHC::with('layanan_hc')->where('status_pasien', 'selesai')
            ->whereBetween('tanggal_kunjungan', [$startDate, $endDate])
            ->orderBy('created_at','ASC')->get();
        }
		$this->data = $data;
	}
}
