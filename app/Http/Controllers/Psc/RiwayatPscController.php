<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanAmbulance;
use App\Models\LayananAmbulance;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class RiwayatPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Riwayat Ambulance';
	}

    public function main(Request $request) {
        if(request()->ajax()){
            $data = PermintaanAmbulance::where('tanggal_kunjungan', $request->tanggal)
                ->orderBy('created_at','ASC')->get();
			return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('nama_layanan', function($row){
                    if (!empty($row->id_layanan_ambulance)) {
                        $layanan = LayananAmbulance::where('id_layanan_ambulance', $row->id_layanan_ambulance)->first()->nama_layanan;
                    } else {
                        $layanan = '';
                    }
					return $layanan;
				})
                ->addColumn('status', function($row){
                    if ($row->status_pasien == 'selesai') {
                        $text = 'SELESAI';
                    } else if($row->status_pasien != 'batal' && $row->status_pasien != 'selesai') {
                        $text = "<a href='javascript:void(0)' style='color: #000' onclick='lanjutkan(`$row->id_permintaan_hc`)'>DILANJUTKAN</a>";
                    }
                    return $text;
                })
                ->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.psc.riwayat.main', $data);
    }
}
