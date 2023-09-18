<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LayananPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Layanan Ambulance';
	}

    public function main() {
        $data['title'] = $this->title;
        return view('admin.psc.layanan.main', $data);
    }
    
}
