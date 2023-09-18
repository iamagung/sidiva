<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermintaanPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Permintaan Ambulance';
	}

    public function main() {
        $data['title'] = $this->title;
        return view('admin.psc.permintaan.main', $data);
    }
    
}
