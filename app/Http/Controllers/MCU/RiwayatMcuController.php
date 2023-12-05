<?php

namespace App\Http\Controllers\MCU;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanMcu;
use App\Models\LayananMcu;
use App\Models\LayananPermintaanMcu;
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
            $data = PermintaanMcu::whereIn('status_pasien',['batal','tolak','selesai'])
                ->whereBetween('tanggal_kunjungan', [$request->min, $request->max])
                ->orderBy('id_permintaan','DESC')->get();
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
                ->addColumn('modifyLayanan', function($row){
                    $text = !empty($row->listLayanan)?$row->listLayanan:'-';
					return $text;
				})
                ->addColumn('modifyNorm', function($row){
                    if (!empty($row->no_rm)) {
                        $layanan = $row->no_rm;
                    } else {
                        $layanan = '-';
                    }
					return $layanan;
				})
                ->addColumn('modifyJenis', function($row){
                    $text = strtoupper($row->jenis_mcu);
					return $text;
				})
                ->addColumn('modifyStatus', function($row){
                    $text = strtoupper($row->status_pasien);
					return $text;
				})
				->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.mcu.riwayat.main', $data);
    }
}
