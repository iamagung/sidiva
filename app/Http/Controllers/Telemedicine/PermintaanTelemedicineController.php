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
use App\Models\ResepObat;
use App\Models\VideoConference;
use App\Models\TenagaMedisTelemedicine;
use App\Models\Activity;
use App\Helpers\Helpers as Help;
use App\Helpers\XenditHelpers;
// use Illuminate\Support\Facades\Auth;
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
                    ->with('resep_obat')
                    ->orderBy('permintaan_telemedicine.created_at','ASC');
                $data->when($request->status=='all',fn($q) =>
                    $q->whereIn('status_pasien', ['belum','menunggu','proses','batal','tolak','selesai'])
                );
                $data->when($request->status!='all',fn($q) =>
                    $q->where('status_pasien', $request->status)
                );
                $data->get();
            } else {
                $data = PermintaanTelemedicine::orderBy('created_at','ASC')->get();
            }
			return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('nama_nakes', function($row){
                    // return $row->nakes->name;
					if ($row->nakes == null) {
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
                    if($row->status_pasien == 'belum') {
                        $txt = "
                        <button class='btn btn-sm btn-primary' title='terima' onclick='terima(`$row->id_permintaan_telemedicine`)'>Terima</button>
                        <button class='btn btn-sm btn-danger' title='tolak' onclick='tolak(`$row->id_permintaan_telemedicine`)'>Tolak</button>
                        ";
                    } else if($row->status_pasien == 'menunggu'){
                        $txt = "
                        <button class='btn btn-sm btn-success' title='jadwal' onclick='jadwal(`$row->id_permintaan_telemedicine`)'>Pilih Jadwal</button>
                        ";
                    } else if(in_array($row->status_pasien,['tolak', 'batal'])){
                        $txt = "-";
                    } else if($row->status_pasien == 'proses'){
                        $txt = "
                        <button class='btn btn-sm btn-warning' title='jadwal' onclick='jadwal(`$row->id_permintaan_telemedicine`)'>Reschedule</button>
                        ";
                    } else {
                        $txt = "
                        <button class='btn btn-sm btn-secondary' title='detail' onclick='detail(`$row->id_permintaan_telemedicine`)'>Detail</button>
                        ";
                        if($row->resep_obat != '') {
                            $txt.= "<button class='btn btn-sm btn-primary' title='eresep' onclick='detailEresep(`$row->id_permintaan_telemedicine`)'>Eresep</button>";
                        }
                    }
					// if (in_array($row->status_pembayaran, ['belum', 'batal'])) {
                    //     if($row->status_pasien == 'belum') {
                        //         $txt = "
                        //         <button class='btn btn-sm btn-primary' title='terima' onclick='terima(`$row->id_permintaan_telemedicine`)'>Terima</button>
                        //         <button class='btn btn-sm btn-danger' title='tolak' onclick='tolak(`$row->id_permintaan_telemedicine`)'>Tolak</button>
                    //         ";
                    //     } else {
                    //         if (in_array($row->status_pasien, ['tolak','batal'])) {
                    //             $txt = "-";
                    //         } else {
                    //             $txt = "
                    //             <button class='btn btn-sm btn-success' title='jadwal' onclick='jadwal(`$row->id_permintaan_telemedicine`)'>Pilih Jadwal</button>
                    //             ";
                    //         }
                    //     }
                    // } else {
                    //     if($row->perawat_id != '' && $row->perawat_id != null) {
                    //         $txt = "
                    //         <button class='btn btn-sm btn-secondary' title='jadwal' onclick='jadwal(`$row->id_permintaan_telemedicine`)'>Reschedule</button>
                    //         ";
                    //     } else {
                    //         $txt = "
                    //         <button class='btn btn-sm btn-warning' title='jadwal' onclick='jadwal(`$row->id_permintaan_telemedicine`)'>Reschedule</button>
                    //         ";
                    //     }
                    // }
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
                    if (in_array($row->status_pembayaran, ['paid', 'lunas'])) {
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
                $data['tenagaMedis'] = TenagaMedisTelemedicine::select('nakes_user.name as nama_nakes', 'nakes_id')->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'tenaga_medis_telemedicine.nakes_id','=','nakes_user.id')->where('tenaga_medis_telemedicine.jenis_nakes','perawat')->where('tenaga_medis_telemedicine.poli_id', $permintaan->poli_id)->get();
                $data['tenagaMedisDokter'] = TenagaMedisTelemedicine::select('nakes_user.name as nama_nakes', 'nakes_id')->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'tenaga_medis_telemedicine.nakes_id','=','nakes_user.id')->where('tenaga_medis_telemedicine.jenis_nakes','dokter')->where('tenaga_medis_telemedicine.poli_id', $permintaan->poli_id)->get();
                $data['nama_dokter'] = TenagaMedisTelemedicine::select('nakes_user.name')->join(DB::connection('dbranap')->raw('wahidin_ranap.users as nakes_user'),'tenaga_medis_telemedicine.nakes_id','=','nakes_user.id')->where('nakes_id',$permintaan->tenaga_medis_id)->first();
                $data['form_detail'] = isset($request->form_detail) ? true : false;
                $data['title'] = "Pilih Tenaga Telemedicine";
                $content = view('admin.telemedicine.permintaan.modal', $data)->render();
                return ['status' => 'success', 'content' => $content, 'data' => $data];
            }
        }
        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data permintaan tidak ditemukan'];
    }

    public function formEresep(Request $request)
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
                ->with('resep_obat', function($q) {
                    $q->with('resep_obat_detail');
                })
                ->with('payment_permintaan_eresep')
                ->first();

            if($permintaan) {
                $data['permintaan'] = $permintaan;
                $content = view('admin.telemedicine.permintaan.modalEresep', $data)->render();
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

            DB::beginTransaction();

            try {

                $data = PermintaanTelemedicine::where('id_permintaan_telemedicine', $request->id)->first();

                if($data) {
                    $jadwal_dokter = explode('-',$data->jadwal_dokter);
                    $kunjungan = date('Y-m-d H:i:s', strtotime($data->tanggal_kunjungan.$jadwal_dokter[0]));
                    if(in_array($data->status_pasien, ['menunggu','proses'])) {
                        $data->perawat_id = $request->perawat_id;
                        $data->tenaga_medis_id = $request->nakes_id;
                        $data->status_pasien = 'proses';
                        $data->jadwal_dokter = $request->jadwal_dokter;
                        $data->tanggal_kunjungan = $request->tanggal_kunjungan;
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
                    if($vicon->waktu_mulai != null){
                        if(date('Y-m-d H:i:s', strtotime($vicon->waktu_mulai)) < date('Y-m-d H:i:d')){
                            DB::rollback();
                            return ['code' => 401, 'type' => 'error', 'status' => 'error', 'message' => 'Tidak bisa me-reschedule jadwal yang masih / sudah dilayani'];
                        }
                    }
                    if($kunjungan < date('Y-m-d H:i:s')) {
                        DB::rollback();
                        return ['code' => 401, 'type' => 'error', 'status' => 'error', 'message' => 'Tidak bisa me-reschedule jadwal yang masih / sudah dilayani'];
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
                    if(!$activity = Activity::store(Auth::user()->id,'Menjadwalkan telemedicine')) {
                        DB::rollback();
                        $return = ['code' => 401, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
                    } else {
                        DB::commit();
                        $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
                    }
                } else {
                    DB::rollback();
                    $return = ['code' => 401, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
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
        $rules = array(
            'id' => 'required',
        );
        $messages = array(
            'id.required'  => 'Kolom Id Harus Diisi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        }
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
                $activity = Activity::store(Auth::user()->id,'Menolak permintaan');
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
                    if ($payment = PaymentPermintaan::where('permintaan_id', $request->id)->where('jenis_layanan', 'telemedicine')->where('status', '!=', 'UNCONFIRMED')->first()) {
                        DB::rollback();
                        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Permintaan sudah melalui pembayaran'];
                    }
                    if (!($data->biaya_layanan != null && $data->biaya_layanan != 0)) {
                        DB::rollback();
                        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Biaya Dokter Tidak Valid'];
                    }
                    // $payment = new PaymentPermintaan;
                    // $payment->permintaan_id = $request->id;
                    // $payment->nominal = $data->biaya_layanan;
                    // $payment->jenis_layanan = 'telemedicine';
                    // $payment->tgl_expired = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    // $payment->status = 'UNCONFIRMED';
                    // $payment->save();
                    $data->status_pasien = 'menunggu';
                    $data->save();

                    if ($data) {
                        if(!$activity = Activity::store(Auth::user()->id,'Terima telemedicine')) {
                            DB::rollback();
                            $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
                        } else {
                            DB::commit();
                            $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
                        }
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

    // public function refund() {
    //     $xendit = XenditHelpers::createRefund();
    //     return $xendit;
    // }
}
