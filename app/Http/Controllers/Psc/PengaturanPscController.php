<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengaturanPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Pengaturan';
	}

    public function main() {
        $data['title'] = $this->title;
        return view('admin.psc.pengaturan.main', $data);
    }
    
}
