<?php

namespace App\Http\Controllers\MCU;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanMcu;
use App\Models\LayananMcu;
use App\Models\LayananPermintaanMcu;
use App\Models\TransaksiMCU;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class PermintaanMcuController extends Controller
{
    function __construct(){
		$this->title = 'Permintaan MCU';
	}

    public function main(Request $request)
    {
        if(request()->ajax()){
            if (!empty($request->tanggal && !empty($request->status))) {
                if ($request->status=='all') {
                    $data = PermintaanMcu::where('tanggal_kunjungan', $request->tanggal)
                        ->orderBy('created_at','ASC')->get();
                } else {
                    $data = PermintaanMcu::where('tanggal_kunjungan', $request->tanggal)
                        ->where('status_pasien', $request->status)
                        ->orderBy('created_at','ASC')->get();
                }
            } else {
                $data = PermintaanMcu::where('tanggal_kunjungan', $request->tanggal)
                    ->orderBy('created_at','ASC')->get();
            }
            foreach ($data as $k => $v) {
                $layanan = LayananPermintaanMcu::where('permintaan_id', $v->id_permintaan)->get();
                foreach ($layanan as $key => $val) {
                    $namaLayanan = LayananMcu::where('id_layanan', $val->layanan_id)->first();
                    if (!empty($namaLayanan)) {
                        if(!isset($v->listLayanan)){
                            $v->listLayanan = $namaLayanan->nama_layanan;
                        }else{
                            $v->listLayanan .= ", " . $namaLayanan->nama_layanan;
                        }
                    }
                }
            }
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('opsi', function($row){
                    // if ($row->tanggal_kunjungan!=date('Y-m-d')&&$row->status_pasien=='belum'&&$row->status_pembayaran=='pending') {
                    //     return "
                    //     <button class='btn btn-sm btn-primary' title='terima' onclick='terima(`$row->id_permintaan`)'>Terima</button>
                    //     <button class='btn btn-sm btn-danger' title='tolak' onclick='tolak(`$row->id_permintaan`)'>Tolak</button>
                    //     ";
                    // } else if($row->tanggal_kunjungan==date('Y-m-d')){
                    //     if ($row->status_pasien=='belum') {
                    //         return "
                    //         <button class='btn btn-sm btn-primary disabled' title='terima'>Terima</button>
                    //         <button class='btn btn-sm btn-danger disabled' title='tolak'>Tolak</button>
                    //         ";
                    //     } else if($row->status_pembayaran=='paid') {
                    //         if($row->status_pasien=='menunggu') {
                    //             return "<button class='btn btn-sm btn-success' title='pilih nakes' onclick='pilih(`$row->id_permintaan`)'>PILIH NAKES</button>";
                    //         } else {
                    //             return "<button class='btn btn-sm btn-secondary' title='detail' onclick='detail(`$row->id_permintaan`)'>DETAIL</button>";
                    //         }
                    //     }
                    // } else {
                    //     return '<div class="text-center">-</div>';
                    // }
                    return "<button class='btn btn-sm btn-success' title='pilih nakes' onclick='pilih(`$row->id_permintaan`)'>PILIH JADWAL</button>
                    <button class='btn btn-sm btn-secondary' title='detail' onclick='detail(`$row->id_permintaan`)'>DETAIL</button>";
				})
                ->addColumn('no_rm', function($row){
					return $text = ($row->no_rm)?$row->no_rm:'-';
				})
                ->addColumn('layanan_mcu', function($row){
                    $txt = !empty($row->listLayanan)?$row->listLayanan:'-';
					return $txt;
				})
                ->addColumn('payment', function($row){
                    if ($row->status_pembayaran=='pending') {
                        return 'BELUM BAYAR';
                    } else if ($row->status_pembayaran=='paid') {
                        return 'LUNAS';
                    } else {
                        return '-';
                    }
				})
                ->addColumn('modifyStatus', function($row){
					if ($row->status_pasien=='proses') {
                        return 'PROSES';
                    } else if ($row->status_pasien=='menunggu') {
                        return 'MENUNGGU';
                    } else if ($row->status_pasien=='batal') {
                        return 'DIBATALKAN';
                    } else if ($row->status_pasien=='tolak') {
                        return 'DITOLAK';
                    } else if ($row->status_pasien=='selesai') {
                        return 'SELESAI';
                    } else {
                        return '-';
                    }
				})
				->rawColumns(['opsi'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.mcu.permintaan.main', $data);
    }
    public function tolak(Request $request) {
        try {
            $data = PermintaanMcu::where('id_permintaan', $request->id)->first();
            $data->status_pasien         = 'tolak';
            $data->save();
            if ($data) {
                return ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Berhasil'];
            }
            return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Error'];
        } catch (\Throwable $e) {
            $log = ['ERROR TOLAK PERMINTAAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function terima(Request $request) {
        try {
            $data = PermintaanMcu::where('id_permintaan', $request->id)->first();
            $data->status_pasien = 'menunggu';
            $data->no_registrasi = Help::generateNoRegMcu($data->tanggal_kunjungan);
            $data->save();
            if ($data) {
                return ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Berhasil'];
            }
            return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Error'];
        } catch (\Throwable $e) {
            $log = ['ERROR TERIMA PERMINTAAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function form(Request $request) {
        try {
            $data['view'] = isset($request->view)==1?1:0;
            $data['permintaan'] = PermintaanMcu::where('id_permintaan', $request->id)->first();
            $data['layanan'] = LayananPermintaanMcu::select('layanan_id')
                ->where('permintaan_id',$data['permintaan']->id_permintaan)
                ->get();
            $data['setLayanan'] = collect($data['layanan'])->map(function($dt){
                return $dt->layanan_id;
            })->toArray();
            $data['dtLayanan'] = LayananMcu::all();
            // return $data;
            $content = view('admin.mcu.permintaan.modal', $data)->render();
            return ['status'=>'success', 'message'=>'Berhasil', 'content'=>$content];
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR PILIH JADWAL MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function simpan(Request $request) {
        try {
            $permintaan = PermintaanMcu::where('id_permintaan', $request->id)->first();
            $permintaan->date_mcu = $request->date_choice;
            $permintaan->time_mcu = $request->time_choice;
            $permintaan->status_pasien = 'proses';
            $permintaan->save();
            if (!$permintaan) {
                return ['code' => 400, 'status' => 'error', 'message' => 'Gagal'];
            }
            return ['code' => 200, 'status' => 'success', 'message' => 'Ok'];
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SIMPAN PEMBAYARAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function proses(Request $request)
    {
        $data = PermintaanMcu::where('id_permintaan', $request->id)->first();
        if ($data->status_pembayaran == null) {
            return ['code' => 205, 'status' => 'warning', 'message' => 'Pasien Belum Melakukan Pembayaran.'];
        }
        try {
            if (!empty($data)) {
                $noRm = Help::generateRM(); # Generate no rm
                $data->status_pasien = 'proses';
                $data->no_rm = $noRm;
                $data->save();

                # insert to tabel tm_customer
                if(!$tmCustomer = DB::connection('dbrsud')->table('tm_customer')->where('KodeCust',$noRm)->first()){
                    $dtCustomer = [
                        'KodeCust' => $noRm,
                        'NoKtp'    => $data->nik,
                        'NamaCust' => $data->nama,
                        'TglLahir' => $data->tanggal_lahir,
                        'Alamat'   => $data->alamat,
                        'jenisKel' => $data->jenis_kelamin,
                        'Telp'     => $data->telepon,
                    ];
                    DB::connection('dbrsud')->table('tm_customer')->insert($dtCustomer);
                }

                return ['code' => 200, 'status' => 'success', 'message' => 'Berhasil di proses.'];
            } else {
                return ['code' => 201, 'status' => 'success', 'message' => 'Gagal di proses.'];
            }
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR PROSES PERMINTAAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
