<?php

namespace App\Http\Controllers\MCU;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SyaratMcu;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class SyaratMcuController extends Controller
{
    function __construct()
	{
		$this->title = 'Syarat & Aturan';
	}

    public function main()
    {
        $syaratAturan = SyaratMcu::get();
        if (count($syaratAturan) > 0) {
            $data['syarat'] = SyaratMcu::where('id_syarat_mcu', 1)->first();
        } else {
            $data['data'] = '';
        }
        $data['title'] = $this->title;
        return view('admin.mcu.syarat.main', $data);
    }

    public function store(Request $request)
    {
        if (empty($request->id)) {
			$syarat = new SyaratMcu;
		}else{
			$syarat = SyaratMcu::where('id_syarat_mcu', $request->id)->first();
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
