<?php

namespace App\Http\Controllers\Telemedicine;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanTelemedicine;
use App\Models\PaketTelemedicine;
use App\Models\PaymentPermintaan;
use App\Models\JadwalTenagaMedis;
use App\Models\LayananTelemedicine;
use App\Models\TenagaMedis;
use App\Models\VideoConference;
use App\Models\TenagaMedisTelemedicine;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class PermintaanTelemedicineController extends Controller
{
    function __construct()
	{
		$this->title = 'Permintaan Baru';
	}

    public function main(Request $request)
    {
        if(request()->ajax()){
            if (!empty($request->tanggal) && !empty($request->status)) {
                $data = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order', 'no_rm', 'nama', 'permintaan_telemedicine.no_telepon', 'poli_id', 'tenaga_medis_id', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pembayaran', 'status_pasien', 'perawat_id')
                    ->where('tanggal_kunjungan', $request->tanggal)
                    ->with('tmPoli:KodePoli,NamaPoli')
                    ->with('nakes:id,name')
                    ->orderBy('permintaan_telemedicine.created_at','ASC');
                $data->when($request->status=='all',fn($q) =>
                    $q->whereIn('status_pasien', ['belum','menunggu','proses','batal','tolak','selesai'])
                );
                $data->when($request->status!='all',fn($q) =>
                    $q->where('status_pasien', $request->status)
                );
                $data->get();
                // if ($request->status=='all') {
                //     // $data = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order', 'no_rm', 'nama', 'permintaan_telemedicine.no_telepon', 'poli_id', 'tenaga_medis_id', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pembayaran', 'status_pasien', 'perawat_id')
                //     // ->where('tanggal_kunjungan', $request->tanggal)
                //     // ->with('tmPoli:KodePoli,NamaPoli')
                //     // ->with('nakes:id,name')
                //     // ->with(['dokter' => function ($q) {
                //     //     $q->select('nakes_id')->whereHas('userRanap')->with('userRanap:name as nama_dokter,id');
                //     // }])
                //     // $data = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order', 'no_rm', 'nama', 'permintaan_telemedicine.no_telepon', 'tm_poli.NamaPoli', 'nakes_user.name as nama_nakes', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pembayaran', 'status_pasien', 'perawat_id')
                //     // ->where('tanggal_kunjungan', $request->tanggal)
                //     // ->join(DB::connection('dbrsud')->raw('dbsimars_baru.tm_poli as tm_poli'),'permintaan_telemedicine.poli_id','=','tm_poli.KodePoli')
                //     // // ->join('tenaga_medis_telemedicine as nakes', 'permintaan_telemedicine.tenaga_medis_id', '=', 'nakes.nakes_id')
                //     // ->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'permintaan_telemedicine.tenaga_medis_id','=','nakes_user.id')
                //     ->whereIn('status_pasien', ['belum','menunggu','proses','batal','tolak','selesai'])
                //     ->orderBy('permintaan_telemedicine.created_at','ASC')
                //     ->get();
                // } else {
                //     // $data = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order', 'no_rm', 'nama', 'permintaan_telemedicine.no_telepon', 'poli_id', 'tenaga_medis_id', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pembayaran', 'status_pasien', 'perawat_id')
                //     // ->where('tanggal_kunjungan', $request->tanggal)
                //     // ->with('tmPoli:KodePoli,NamaPoli')
                //     // ->with('nakes:id,name')
                //     // ->with(['dokter' => function ($q) {
                //     //     $q->select('nakes_id')->whereHas('userRanap')->with('userRanap:id,name');
                //     // }])
                //     // $data = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'tanggal_order', 'no_rm', 'nama', 'permintaan_telemedicine.no_telepon', 'tm_poli.NamaPoli', 'nakes_user.name as nama_nakes', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pembayaran', 'status_pasien', 'perawat_id')
                //     // ->where('tanggal_kunjungan', $request->tanggal)
                //     // ->join(DB::connection('dbrsud')->raw('dbsimars_baru.tm_poli as tm_poli'),'permintaan_telemedicine.poli_id','=','tm_poli.KodePoli')
                //     // // ->join('tenaga_medis_telemedicine as nakes', 'permintaan_telemedicine.tenaga_medis_id', '=', 'nakes.nakes_id')
                //     // ->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'permintaan_telemedicine.tenaga_medis_id','=','nakes_user.id')
                //     ->where('status_pasien', $request->status)
                //     ->orderBy('permintaan_telemedicine.created_at','ASC')
                //     ->get();
                // }
                // return $data;
            } else {
                $data = PermintaanTelemedicine::orderBy('created_at','ASC')->get();
            }
			return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('nama_nakes', function($row){
                    // return $row->nakes->name;
					if ($row->dokter == null) {
                        $txt = "
                        <span class='text-danger text-center'>Tidak ada data dokter</span>
                        ";
                    } else {
                        $txt = "
                        <span class=' text-center'>".$row->nakes->name."</span>
                        ";
                    }
					return $txt;
				})
                ->addColumn('nama_poli', function($row){
					if ($row->tmPoli == null) {
                        $txt = "
                        <span class='text-danger text-center'>Tidak ada data poli</span>
                        ";
                    } else {
                        $txt = "
                        <span class=' text-center'>".$row->tmPoli->NamaPoli."</span>
                        ";
                    }
					return $txt;
				})
				->addColumn('opsi', function($row){
					if ($row->status_pembayaran == 'belum') {
                        if($row->status_pasien == 'belum') {
                            $txt = "
                            <button class='btn btn-sm btn-primary' title='terima' onclick='terima(`$row->id_permintaan_telemedicine`)'>Terima</button>
                            <button class='btn btn-sm btn-danger' title='tolak' onclick='tolak(`$row->id_permintaan_telemedicine`)'>Tolak</button>
                            ";
                        } else {
                            if ($row->status_pasien == 'tolak') {
                                $txt = "-";
                            } else {
                                $txt = "
                                <button class='btn btn-sm btn-success' title='jadwal' onclick='jadwal(`$row->id_permintaan_telemedicine`)' disabled>Pilih Jadwal</button>
                                ";
                            }
                        }
                    } else {
                        if($row->perawat_id != '' && $row->perawat_id != null) {
                            $txt = "
                            <button class='btn btn-sm btn-secondary' title='detail' onclick='detail(`$row->id_permintaan_telemedicine`)'>Detail</button>
                            ";
                        } else {
                            $txt = "
                            <button class='btn btn-sm btn-success' title='jadwal' onclick='jadwal(`$row->id_permintaan_telemedicine`)'>Pilih Jadwal</button>
                            ";
                        }
                    }
					return $txt;
				})
                ->addColumn('noRm', function($row){
                    if (!empty($row->no_rm)) {
                        $txt = $row->no_rm;
                    } else {
                        $txt = '-';
                    }
					return $txt;
				})
                ->addColumn('statusBayar', function($row){
                    if ($row->status_pembayaran == 'paid') {
                        $txt = "
                        <span class='fw-bold text-center'>LUNAS</span>
                        ";
                    } else {
                        $txt = "
                        <span class='text-center'>BELUM BAYAR</span>
                        ";
                    }
                    return $txt;
				})
				->rawColumns(['statusBayar', 'opsi', 'nama_poli', 'nama_nakes'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.telemedicine.permintaan.main', $data);
    }

    public function form(Request $request)
    {
        $rules = array(
            'id' => 'required',
        );
        $messages = array(
            'required'  => 'Kolom Harus Diisi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        } else {
            $permintaan = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id)
                ->with('video_conference')
                ->first();

            if($permintaan) {
                $data['permintaan'] = $permintaan;
                $data['tenagaMedis'] = TenagaMedisTelemedicine::select('nakes_user.name as nama_nakes', 'nakes_id as id_tenaga_medis')->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'tenaga_medis_telemedicine.nakes_id','=','nakes_user.id')->where('tenaga_medis_telemedicine.jenis_nakes','perawat')->where('tenaga_medis_telemedicine.poli_id', $permintaan->poli_id)->get();
                $data['nama_dokter'] = TenagaMedisTelemedicine::select('nakes_user.name')->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'tenaga_medis_telemedicine.nakes_id','=','nakes_user.id')->where('nakes_id',$permintaan->tenaga_medis_id)->first();
                $data['form_detail'] = isset($request->form_detail) ? true : false;
                $data['title'] = "Pilih Tenaga Telemedicine";
                $content = view('admin.telemedicine.permintaan.modal', $data)->render();
                return ['status' => 'success', 'content' => $content, 'data' => $data];
            }
        }
        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data permintaan tidak ditemukan'];
    }

    public function save(Request $request)
    {
        $rules = array(
            'id' => 'required',
            'perawat_id' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'jadwal_dokter' => 'required|date_format:H:i',
            'link_vicon' => 'required'
        );
        $messages = array(
            'perawat_id.required'  => 'Kolom Perawat Harus Diisi',
            'id.required'  => 'Kolom Id Harus Diisi',
            'tanggal_kunjungan.required' => 'Tanggal Kunjungan Wajib Di isi',
            'tanggal_kunjungan.date' => 'Tanggal Kunjungan harus berformat yyyy-mm-dd',
            'jadwal_dokter.required' => 'Jadwal Dokter Wajib Di isi',
            'jadwal_dokter.date_format' => 'Jadwal Dokter harus berformat H:i, cth:(08:30)',
            'link_vicon.required' => 'Link Meet Wajib Di isi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        } else {
            if (date('Y-m-d H:i:s',strtotime($request->tanggal_kunjungan. " " .$request->jadwal_dokter . ":00")) < date('Y-m-d H:i:s')) {
                return ['code' => 500, 'type' => 'error', 'status' => 'error', 'message' => 'Jadwal tidak boleh lebih rendah dari jam sekarang'];
            }

            try {
                DB::beginTransaction();
                $data = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id)->first();
                if($data) {
                    if($data->status_pembayaran == 'paid') {
                        $data->perawat_id       = $request->perawat_id;
                        $data->status_pasien         = 'proses';
                        $data->jadwal_dokter    = $request->jadwal_dokter;
                        $data->tanggal_kunjungan    = $request->tanggal_kunjungan;
                        $data->save();
                    } else {
                        $data = false;
                    }
                }

                if ($data) {
                    if(!$vicon = VideoConference::where('permintaan_id', $request->id)->where('jenis_layanan', 'telemedicine')->first()){
                        $vicon = new VideoConference;
                        $vicon->jenis_layanan = 'telemedicine';
                        $vicon->permintaan_id = $request->id;
                        $vicon->is_expired = false;
                    }
                    $vicon->link_vicon = $request->link_vicon;
                    $vicon->save();
                    if ($data->no_rm == null) {
                        $noRm = Help::generateRM();
                        # save to tabel permintaan hc
                        $data->no_rm = $noRm;
                        $data->save();
                        # insert to tabel tm_customer
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
                    DB::commit();
                    $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
                } else {
                    DB::rollback();
                    $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
                }
                return $return;
            } catch (\Throwable $e) {
                DB::rollback();
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR SIMPAN TENAGA MEDIS ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }
    }

    public function tolak(Request $request)
    {
        try {
            $data = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id)->first();
            if($data) {
                if($data->status_pasien == 'belum') {
                    $data->status_pasien         = 'tolak';
                    $data->save();
                } else {
                    $data = false;
                }
            }

            if ($data) {
                $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Pasien Berhasil Ditolak'];
            } else {
                $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Pasien Gagal Ditolak'];
            }
            return $return;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SIMPAN TENAGA MEDIS ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function calculateDistance($latitude, $longitude)
    {
        $lat1 = "-7.4906403"; // latitude rsud wahidin
        $lon1 = "112.4178198"; // longitude rsud wahidin
        $lat2 = $latitude;
        $lon2 = $longitude;

        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1))) * sin(deg2rad($lat2)) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $rMiles = $miles * 60 * 1.1515;
        $kilometers = $rMiles * 1.609344;
        $return = intval($kilometers).' KM';
        return $return;
    }

    public function terima(Request $request)
    {
        $rules = array(
            'id' => 'required',
        );
        $messages = array(
            'required'  => 'Kolom Harus Diisi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        } else {
            try {
                DB::beginTransaction();
                $data = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id)->first();
                if($data){
                    if($data->status_pembayaran != 'belum') {
                        DB::rollback();
                        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Status pembayaran tidak sesuai'];
                    }
                    if($data->status_pasien != 'belum') {
                        DB::rollback();
                        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Status pasien tidak dalam permintaan'];
                    }
                    if ($payment = PaymentPermintaan::where('permintaan_id', $request->id)->where('jenis_layanan', 'telemedicine')->first()) {
                        DB::rollback();
                        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Permintaan sudah melalui pembayaran'];
                    }
                    if (!($data->biaya_layanan != null && $data->biaya_layanan != 0)) {
                        DB::rollback();
                        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Biaya Dokter Tidak Valid'];
                    }
                    $payment = new PaymentPermintaan;
                    $payment->permintaan_id = $request->id;
                    $payment->nominal = $data->biaya_layanan;
                    $payment->jenis_layanan = 'telemedicine';
                    $payment->tgl_expired = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    $payment->status = 'UNCONFIRMED';
                    $payment->save();
                    $data->status_pasien = 'menunggu';
                    $data->save();

                    if ($data) {
                        DB::commit();
                        $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
                    }
                } else {
                    DB::rollback();
                    $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
                }
                return $return;
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR SIMPAN TERIMA PERMINTAAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }
    }
}
