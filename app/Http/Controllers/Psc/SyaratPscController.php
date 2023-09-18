<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SyaratPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Syarat dan Ketentuan Layanan Ambulance';
	}

    public function main() {
        $data['title'] = $this->title;
        return view('admin.psc.syarat.main', $data);
    }
}
