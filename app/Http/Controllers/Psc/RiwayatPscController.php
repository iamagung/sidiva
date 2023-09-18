<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Riwayat Ambulance';
	}

    public function main() {
        $data['title'] = $this->title;
        return view('admin.psc.riwayat.main', $data);
    }

    public function emergency() {
        $data['title'] = $this->title;
        return view('admin.psc.emergency.main', $data);
    }

    public function syarat() {
        $data['title'] = $this->title;
        return view('admin.psc.syarat.main', $data);
    }
}
