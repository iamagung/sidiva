<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengaturanAmbulance;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class PengaturanPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Pengaturan';
	}

    public function main() {
        $pengaturanAmbulance = PengaturanAmbulance::get();
        if (count($pengaturanAmbulance) > 0) {
            $data['pengaturan'] = PengaturanAmbulance::where('id_pengaturan_ambulance', 1)->first();
        } else {
            $data['pengaturan'] = '';
        }
        $data['title'] = $this->title;
        return view('admin.psc.pengaturan.main', $data);
    }
    
    public function store(Request $request)
    {
        // return $request->all();
        if (empty($request->id)) {
			$pengaturan = new PengaturanAmbulance;
		}else{
			$pengaturan = PengaturanAmbulance::find($request->id);
		}
		$pengaturan->jarak_maksimal = $request->jarak_maksimal;
		$pengaturan->biaya_per_km = preg_replace("/[^0-9]/", "", $request->biaya_per_km);
		$pengaturan->informasi_pembatalan = $request->informasi_pembatalan;
		$pengaturan->save();

		if ($pengaturan) {
			$data = ['code' => 200, 'status' => 'success', 'message' => 'Berhasil Menyimpan Data'];
		}else{
			$data = ['code' => 201, 'status' => 'success', 'message' => 'Gagal Menyimpan Data'];
		}
		return $data;
    }
}
