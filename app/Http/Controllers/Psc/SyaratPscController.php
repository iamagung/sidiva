<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SyaratAmbulance;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class SyaratPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Syarat dan Ketentuan Layanan Ambulance';
	}

    public function main() {
        $syaratAturan = SyaratAmbulance::get();
        if (count($syaratAturan) > 0) {
            $data['syarat'] = SyaratAmbulance::where('id_syarat_aturan_ambulance', 1)->first();
        } else {
            $data['syarat'] = '';
        }
        $data['title'] = $this->title;
        return view('admin.psc.syarat.main', $data);
    }

    public function store(Request $request)
    {
        // return $request->all();
        if (empty($request->id)) {
			$syarat = new SyaratAmbulance;
		}else{
			$syarat = SyaratAmbulance::find($request->id);
		}
		$syarat->syarat_aturan = $request->syarat_aturan;
		$syarat->save();

		if ($syarat) {
			$data = ['code' => 200, 'status' => 'success', 'message' => 'Berhasil Menyimpan Data'];
		}else{
			$data = ['code' => 201, 'status' => 'success', 'message' => 'Gagal Menyimpan Data'];
		}
		return $data;
    }
}
