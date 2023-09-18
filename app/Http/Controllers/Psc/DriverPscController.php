<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DriverPscController extends Controller
{
    function __construct()
	{
		$this->title = 'Driver Ambulance';
	}

    public function main() {
        $data['title'] = $this->title;
        return view('admin.psc.driver.main', $data);
    }
    
}
