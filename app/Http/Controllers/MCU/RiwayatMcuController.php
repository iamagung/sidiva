<?php

namespace App\Http\Controllers\MCU;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanMcu;
use App\Models\LayananMcu;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class RiwayatMcuController extends Controller
{
    function __construct()
	{
		$this->title = 'Riwayat MCU';
	}

    public function main(Request $request)
    {
        if(request()->ajax()){
            $data = PermintaanMcu::where('tanggal_kunjungan', $request->tanggal)
                ->where('status_pasien', 'selesai')
                ->orderBy('created_at','ASC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
                    // <a href='javascript:void(0)' style='color: #000' onclick='downloadMcu(`$row->id_permintaan`)'>download</a>
					$txt = "
                      <button class='btn btn-sm btn-success' title='proses' onclick='downloadHasil(`$row->id_permintaan`)'><i class='bx bxs-download'></i> Download</button>
					";
					return $txt;
				})
                ->addColumn('jenis_layanan', function($row){
                    if (!empty($row->layanan_id)) {
                        $layanan = LayananMcu::where('id_layanan', $row->layanan_id)->first()->jenis_layanan;
                    } else {
                        $layanan = '-';
                    }
					return $layanan;
				})
                ->addColumn('nama_layanan', function($row){
                    if (!empty($row->layanan_id)) {
                        $layanan = LayananMcu::where('id_layanan', $row->layanan_id)->first()->nama_layanan;
                    } else {
                        $layanan = '-';
                    }
					return $layanan;
				})
                ->addColumn('rm', function($row){
                    if (!empty($row->no_rm)) {
                        $layanan = $row->no_rm;
                    } else {
                        $layanan = '-';
                    }
					return $layanan;
				})
				->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.mcu.riwayat.main', $data);
    }
}
