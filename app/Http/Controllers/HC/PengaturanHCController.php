<?php

namespace App\Http\Controllers\HC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengaturanHC;

class PengaturanHCController extends Controller
{
    function __construct()
	{
		$this->title = 'Pengaturan';
	}

    public function form(Request $request)
    {
        $data['title'] = $this->title;
        return view('admin.homecare.pengaturan.main', $data);
    }

    public function get(Request $request)
    {
        $data = PengaturanHC::where('id_pengaturan_hc', 1)->first();
        if ($data) {
            return ['code' => 200, 'status' => 'sucess', 'message' => 'Data Ditemukan', 'jadwal' => $data];
        } else {
            return ['code' => 201, 'status' => 'error', 'message' => 'Data Tidak Ditemukan', 'jadwal' => ''];
        }
    }

    public function store(Request $request)
    {
        if ($request->biaya_per_km == null) {
            return ['code' => 500, 'status' => 'warning', 'message' => 'Biaya Per KM Wajib Diisi'];
        }
        if ($request->jarak_maksimal == null) {
            return ['code' => 500, 'status' => 'warning', 'message' => 'Jarak Maksimal Wajib Diisi'];
        }
        $pengaturan = PengaturanHC::all();
        if (count($pengaturan) > 0) {
            PengaturanHC::truncate();
        }
        $data = new PengaturanHC;
        if ($request->senin == "on") {
            $data->seninBuka    = $request->buka1;
            $data->seninTutup   = $request->tutup1;
        }
        if ($request->selasa == "on") {
            $data->selasaBuka   = $request->buka2;
            $data->selasaTutup  = $request->tutup2;
        }
        if ($request->rabu == "on") {
            $data->rabuBuka     = $request->buka3;
            $data->rabuTutup    = $request->tutup3;
        }
        if ($request->kamis == "on") {
            $data->kamisBuka    = $request->buka4;
            $data->kamisTutup   = $request->tutup4;
        }
        if ($request->jumat == "on") {
            $data->jumatBuka    = $request->buka5;
            $data->jumatTutup   = $request->tutup5;
        }
        if ($request->sabtu == "on") {
            $data->sabtuBuka    = $request->buka6;
            $data->sabtuTutup   = $request->tutup6;
        }
        if ($request->minggu == "on") {
            $data->mingguBuka   = $request->buka7;
            $data->mingguTutup  = $request->tutup7;
        }
        $data->biaya_per_km     = preg_replace("/[^0-9]/", "", $request->biaya_per_km);
        $data->jarak_maksimal   = $request->jarak_maksimal;
        $data->batas_waktu      = $request->batas_waktu;
        $data->save();

        if ($data) {
            return ['code' => 200, 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
        } else {
            return ['code' => 201, 'status' => 'error', 'message' => 'Data Gagal Disimpan'];
        }
    }
}
