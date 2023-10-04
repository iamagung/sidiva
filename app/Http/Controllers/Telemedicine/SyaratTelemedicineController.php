<?php

namespace App\Http\Controllers\Telemedicine;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SyaratTelemedicine;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class SyaratTelemedicineController extends Controller
{
    function __construct()
	{
		$this->title = 'Syarat & Aturan';
	}

    public function main()
    {
        $syaratAturan = SyaratTelemedicine::get();
        if (count($syaratAturan) > 0) {
            $data['syarat'] = SyaratTelemedicine::where('id_syarat_telemedicine', 1)->first();
        } else {
            $data['data'] = '';
        }
        $data['title'] = $this->title;
        return view('admin.telemedicine.syarat.main', $data);
    }

    public function store(Request $request)
    {
        if (empty($request->id)) {
			$syarat = new SyaratTelemedicine;
		}else{
			$syarat = SyaratTelemedicine::where('id_syarat_telemedicine', $request->id)->first();
		}
		$syarat->isi = $request->isi;
		$syarat->save();

		if ($syarat) {
			$data = ['code' => 200, 'status' => 'success', 'message' => 'Berhasil Menyimpan Data'];
		}else{
			$data = ['code' => 201, 'status' => 'success', 'message' => 'Gagal Menyimpan Data'];
		}
		return $data;
    }
}