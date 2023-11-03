<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanAmbulance;
use App\Models\LayananAmbulance;
use DataTables, Validator, DB, Auth;

class PermintaanPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Permintaan Ambulance';
	}

    public function main(Request $request)
    {
        if(request()->ajax()){
            $data = PermintaanAmbulance::where('tanggal_order', $request->tanggal)
                ->where('status_pasien', 'belum')
                ->orderBy('created_at','ASC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					$txt = "
                        <button class='btn btn-sm btn-primary' title='bayar' onclick='formAdd(`$row->id_permintaan_ambulance`)'>Proses</button>
                        <button class='btn btn-sm btn-danger' title='batal' onclick='batal(`$row->id_permintaan_ambulance`)'><i class='bx bx-x' style='color:#ffffff'></i></button>
                        ";
					return $txt;
				})
                ->addColumn('layanan', function($row){
                    return $txt = LayananAmbulance::where('id_layanan_ambulance', $row->jenis_layanan)->first()->nama_layanan;
				})
                // ->addColumn('pembayaran', function($row){
                //     if (!empty($row->status_pembayaran)) {
                //         $layanan = 'LUNAS';
                //     } else {
                //         $layanan = 'BELUM BAYAR';
                //     }
				// 	return $layanan;
				// })
				->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.psc.permintaan.main', $data);
    }

    public function form(Request $request)
    {
        if (empty($request->id)) {
            $data['data'] = '';    
		}else{
            $data['data'] = PermintaanAmbulance::where('id_permintaan_ambulance',$request->id)->first();
		}
        $content = view('admin.psc.permintaan.modal', $data)->render();
		return ['content'=>$content];
    }

}
