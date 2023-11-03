<?php

namespace App\Http\Controllers\MCU;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanMcu;
use App\Models\LayananMcu;
use App\Models\TransaksiMCU;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class PermintaanMcuController extends Controller
{
    function __construct()
	{
		$this->title = 'Permintaan Telemedicine';
	}

    public function main(Request $request)
    {
        if(request()->ajax()){
            $data = PermintaanMcu::where('tanggal_kunjungan', $request->tanggal)
                ->where('status_pasien', 'belum')
                ->orderBy('created_at','ASC')->get();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					if ($row->status_pembayaran == 'sudah') {
                        $txt = "
                        <button class='btn btn-sm btn-primary' title='bayar' onclick='bayar(`$row->id_permintaan`)' disabled>Bayar</button>
                        <button class='btn btn-sm btn-success' title='proses' onclick='proses(`$row->id_permintaan`)'>Proses</button>
                        ";
                    } else {
                        $txt = "
                        <button class='btn btn-sm btn-primary' title='bayar' onclick='bayar(`$row->id_permintaan`)'>Bayar</button>
                        <button class='btn btn-sm btn-success' title='proses' onclick='proses(`$row->id_permintaan`)' disabled>Proses</button>
                        ";
                    }
					return $txt;
				})
                ->addColumn('jenis_layanan', function($row){
                    if (!empty($row->layanan_id)) {
                        $layanan = LayananMcu::where('id_layanan', $row->layanan_id)->first()->jenis_layanan;
                    } else {
                        $layanan = '';
                    }
					return $layanan;
				})
                ->addColumn('nama_layanan', function($row){
                    if (!empty($row->layanan_id)) {
                        $layanan = LayananMcu::where('id_layanan', $row->layanan_id)->first()->nama_layanan;
                    } else {
                        $layanan = '';
                    }
					return $layanan;
				})
                ->addColumn('deskripsi', function($row){
                    if (!empty($row->layanan_id)) {
                        $layanan = LayananMcu::where('id_layanan', $row->layanan_id)->first()->deskripsi;
                    } else {
                        $layanan = '';
                    }
					return $layanan;
				})
                ->addColumn('pembayaran', function($row){
                    if (!empty($row->status_pembayaran)) {
                        $layanan = 'LUNAS';
                    } else {
                        $layanan = 'BELUM BAYAR';
                    }
					return $layanan;
				})
				->rawColumns(['actions'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.mcu.permintaan.main', $data);
    }

    public function form(Request $request)
    {
        try {
            $permintaan = PermintaanMcu::leftjoin('layanan_mcu as lm', 'lm.id_layanan', 'permintaan_mcu.layanan_id')
                ->where('id_permintaan', $request->id)->first();
            $id_layanan = explode(",", $permintaan->layanan_id);
            $layanan = LayananMcu::whereIn('id_layanan',$id_layanan)->get();

            if (empty($permintaan)) {
                return ['status'=>'success', 'message'=>'Data tidak ditemukan', 'content'=>$content];
            }
            $data = [
                'data' => $permintaan,
                'layanan' => $layanan
            ];
            $content = view('admin.mcu.permintaan.modal', $data)->render();
            return ['status'=>'success', 'message'=>'Berhasil', 'content'=>$content];
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR BAYAR PERMINTAAN MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function simpan(Request $request)
    {
        try {
            $permintaan = PermintaanMcu::where('id_permintaan', $request->id)->first();
            $permintaan->biaya             = preg_replace("/[^0-9]/", "", $request->total);
            $permintaan->status_pembayaran = 'sudah';
            $permintaan->save();

            if ($permintaan) {
                $transaksi = TransaksiMCU::where('id_permintaan_mcu', $request->id)->first();
                // $transaksi->id_permintaan_mcu = $request->id;
                // $transaksi->nominal           = preg_replace("/[^0-9]/", "", $request->jumlah_bayar);
                $transaksi->status            = 'PAID';
                // $transaksi->invoice           = "INV/".date('Ymd')."/MCU"."/".rand(20, 200);
                $transaksi->save();

                return ['code' => 200, 'status' => 'success', 'message' => 'Berhasil di bayar.'];
            } else {
                return ['code' => 201, 'status' => 'success', 'message' => 'Gagal di bayar.'];
            }
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

    public function invoiceMcu($id)
    {
        // return 'asiap';
        try{
            $permintaan = PermintaanMcu::where('id_permintaan', $id)->first();
            $pasien = DB::connection('dbrsud')->table('tm_customer')->where('NoKtp', $permintaan->nik)->first();
            $id_layanan = explode(",", $permintaan->layanan_id);
            $layanan = DB::table('layanan_mcu')->whereIn('id_layanan', $id_layanan)->get();
            $sum = 0;
            // return $layanan;
            for($i = 0; $i < count($layanan); $i++){
                $sum += $layanan[$i]->harga;
            }
            # update to permintaan_mcu
            $permintaan->biaya = $sum;
            $permintaan->metode_pembayaran = 'Tunai';
            $permintaan->save();
            # insert to transaksi_mcu
            $transaksi = TransaksiMCU::where('id_permintaan_mcu', $id)->whereDate('created_at', '=', date('Y-m-d'))->first();
            if(empty($transaksi)) {
                $transaksi = new TransaksiMCU;
                $transaksi->id_permintaan_mcu = $id;
                $transaksi->nominal           = $sum;
                $transaksi->invoice           = "INV/".date('Ymd')."/MCU"."/".rand(20, 200);
                $transaksi->status            = 'pending';
                $transaksi->save();
            }

            $data = [
                'permintaan'    => $permintaan,
                'pasien'        => $pasien,
                'layanan'       => $layanan,
                'transaksi'     => $transaksi
            ];
            // if ($permintaan) {
            //     $return = [
            //         'metaData' => [
            //             "code" => 200,
            //             "message" => 'Berhasil'
            //         ],
            //         'response' => $data
            //     ];
            // } else {
            //     $return = [
            //         'metaData' => [
            //             "code" => 201,
            //             "message" => 'Gagal.'
            //         ],
            //         'response' => []
            //     ];
            // }
            return view('admin.invoice.mcu', $data);

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET INVOICE MCU ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
