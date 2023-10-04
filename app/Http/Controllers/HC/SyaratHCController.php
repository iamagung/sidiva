<?php

namespace App\Http\Controllers\HC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SyaratHC;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class SyaratHCController extends Controller
{
    function __construct()
	{
		$this->title = 'Syarat & Aturan';
	}

    public function main()
    {
        $syaratAturan = SyaratHC::get();
        if (count($syaratAturan) > 0) {
            $data['syarat'] = SyaratHC::where('id_syarat_hc', 1)->first();
        } else {
            $data['data'] = '';
        }
        $data['title'] = $this->title;
        return view('admin.homecare.syarat.main', $data);
    }

    public function store(Request $request)
    {
        if (empty($request->id)) {
			$syarat = new SyaratHC;
            $syarat->isi = $request->isi;
            $syarat->save();
		}else{
			$syarat = DB::table('syarat_hc')
                ->where('id_syarat_hc','=',1)
                ->update(['isi' => $request->isi]);
		}

		if ($syarat) {
			$data = ['code' => 200, 'status' => 'success', 'message' => 'Berhasil Menyimpan Data'];
		}else{
			$data = ['code' => 201, 'status' => 'success', 'message' => 'Gagal Menyimpan Data'];
		}
		return $data;
    }
}
