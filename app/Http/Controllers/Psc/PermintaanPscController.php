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

    // public function main() {
    //     $data['title'] = $this->title;
    //     return view('admin.psc.permintaan.main', $data);
    // }

    public function main(Request $request)
    {
        if(request()->ajax()){
            $data = PermintaanAmbulance::where('tanggal_kunjungan', $request->tanggal)
                ->where('status_pasien', 'belum')
                ->orderBy('created_at','ASC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					if ($row->status_pembayaran == 'sudah') {
                        $txt = "
                        <button class='btn btn-sm btn-primary' title='bayar' onclick='bayar(`$row->id_permintaan_ambulance`)'>Proses</button>
                        <button class='btn btn-sm btn-danger' title='proses' onclick='proses(`$row->id_permintaan_ambulance`)'disabled><i class='bx bx-x' style='color:#ffffff'  ></i></button>
                        ";
                    } else {
                        $txt = "
                        <button class='btn btn-sm btn-primary' title='bayar' onclick='bayar(`$row->id_permintaan_ambulance`)'disabled>Proses</button>
                        <button class='btn btn-sm btn-danger' title='proses' onclick='proses(`$row->id_permintaan_ambulance`)'><i class='bx bx-x' style='color:#ffffff'  ></i></button>
                        ";
                    }
					return $txt;
				})
                // ->addColumn('jenis_layanan', function($row){
                //     if (!empty($row->layanan_id)) {
                //         $layanan = PermintaanAmbulance::where('id_layanan_ambulance', $row->layanan_id)->first()->jenis_layanan;
                //     } else {
                //         $layanan = '';
                //     }
				// 	return $layanan;
				// })
                ->addColumn('nama_layanan', function($row){
                    if (!empty($row->id_layanan_ambulance)) {
                        $layanan = PermintaanAmbulance::where('id_layanan_ambulance', $row->id_layanan_ambulance)->first()->nama_layanan;
                    } else {
                        $layanan = '';
                    }
					return $layanan;
				})
                // ->addColumn('deskripsi', function($row){
                //     if (!empty($row->layanan_id)) {
                //         $layanan = PermintaanAmbulance::where('id_layanan_ambulance', $row->layanan_id)->first()->deskripsi;
                //     } else {
                //         $layanan = '';
                //     }
				// 	return $layanan;
				// })
                ->addColumn('pembayaran', function($row){
                    if (!empty($row->status_pembayaran)) {
                        $layanan = 'LUNAS';
                    } else {
                        $layanan = 'BELUM BAYAR';
                    }
					return $layanan;
				})
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
