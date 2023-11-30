<?php

namespace App\Http\Controllers\Telemedicine;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenagaMedisTelemedicine;
use App\Models\TenagaMedis;
use App\Models\LayananTelemedicine;
use App\Models\JadwalTenagaMedis;
use App\Models\Dokter;
use App\Models\DBRANAP\Users as UserRanap;
use App\Models\DBRSUD\TmPoli;
use App\Helpers\Helpers as Help;
use DataTables, Validator, DB, Auth;

class TenagaMedisController extends Controller
{
    function __construct()
	{
		$this->title = 'Tenaga Medis';
	}

    public function main()
    {
        if(request()->ajax()){
            $data = TenagaMedisTelemedicine::select('id_tenaga_medis', 'tenaga_medis_telemedicine.poli_id', 'tenaga_medis_telemedicine.jenis_nakes', 'no_telepon', 'status', 'tenaga_medis_telemedicine.nakes_id')
                ->with('tmPoli:KodePoli,NamaPoli')
                ->with('user_ranap:id,name as nama_nakes')
                ->orderBy('id_tenaga_medis','DESC')
                ->get();
            // return $data;
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('actions', function($row){
					$txt = "
                      <button class='btn btn-sm btn-primary' title='Edit' onclick='formAdd(`$row->id_tenaga_medis`)'><i class='fadeIn animated bx bxs-edit' aria-hidden='true'></i></button>
                      <button class='btn btn-sm btn-danger' title='Delete' onclick='hapus(`$row->id_tenaga_medis`)'><i class='fadeIn animated bx bxs-trash' aria-hidden='true'></i></button>
					";
					return $txt;
				})
                ->addColumn('poli_layanan', function($row){
                    return $row->tmPoli->NamaPoli;
                })
                ->addColumn('nama_nakes', function($row){
                  //   return $row->userRanap->nama_nakes;
                    return $row->user_ranap?$row->user_ranap->nama_nakes:'-';
                })
                ->addColumn('jadwal', function($row){
                    if($row->jenis_nakes == 'dokter') {
                        $text = "<button class='btn btn-sm btn-primary' title='Edit' onclick='formJadwal(`$row->id_tenaga_medis`)'><i class='fadeIn animated bx bxs-edit' aria-hidden='true'></i></button>";
                    } else {
                        $text = "-";
                    }
                    return $text;
                    // $jadwal = JadwalTenagaMedis::where('nakes_id', $row->nakes_id)->where('jenis_pelayanan', 'telemedicine')->get();
					// $text = "";
                    // if(count($jadwal) > 0) {
                    //     foreach ($jadwal as $key => $value) {
                    //         $text.="<span>".date_format(date_create($value->jam_awal), "H:i")."-".date_format(date_create($value->jam_akhir), "H:i")."</span><br>";
                    //     }
                    // } else {
                    //     $text = "-";
                    // }
                    // return $text;
				})
                ->addColumn('status', function($row){
                    if($row->status == 'melayani') {
                        return "<span class='fw-bold text-danger'>".strtoupper($row->status)."</span>";
                    } else {
                        return "<span >".strtoupper($row->status)."</span>";
                    }
				})
				->rawColumns(['actions','status','jadwal'])
				->toJson();
		}
        $data['title'] = $this->title;
        return view('admin.telemedicine.tenagamedis.main', $data);
    }

    public function form(Request $request)
    {
        if (empty($request->id)) {
            $data['tenaga_medis'] = '';
            $data['title'] = "Tambah ".$this->title;
            $data['jadwal_tenaga_medis'] = [];
		}else{
			$data['tenaga_medis'] = TenagaMedisTelemedicine::where('id_tenaga_medis', $request->id)->first();
            $data['title'] = "Edit ".$this->title;
		}
        if($data['tenaga_medis']){
            $data['jadwal_tenaga_medis'] = JadwalTenagaMedis::where('nakes_id',$data['tenaga_medis']['nakes_id'])->get();
            // return $data;
        }
        // return $data;
        $data['jenisNakes'] = ['dokter', 'perawat'];

        $data['getTenagaMedis'] = UserRanap::select('id','name','level_user')
            ->has('tenaga_medis_telemedicine')
            ->get();
        $data['getPoliLayanan'] = TmPoli::join('mapping_poli_bridging as mpd', 'tm_poli.KodePoli', '=', 'mpd.kdpoli_rs')
            ->select('KodePoli as id_poli','NamaPoli as nama_poli')
            ->get();
        // return $data['getPoliLayanan'];
        # End Get Dokter
        $content = view('admin.telemedicine.tenagamedis.modal', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    public function store(Request $request)
    {
        // return $request->jadwal;
        if($request->jenis_nakes == 'perawat') {
            $rules = array(
                'jenis_nakes' => 'required',
                'poli_id' => 'required',
                'nakes_id' => 'required',
                'no_telepon' => 'required',
            );
        } else {
            $rules = array(
                'jenis_nakes' => 'required',
                'poli_id' => 'required',
                'nakes_id' => 'required',
                'no_telepon' => 'required',
            );
        }

        $messages = array(
            'jenis_nakes.required'  => 'Kolom Jenis Tenaga Kesetan Harus Diisi',
            'poli_id.required'  => 'Kolom Poli Layanan Harus Diisi',
            'nakes_id.required'  => 'Kolom Tenaga Medis Harus Diisi',
            'no_telepon.required'  => 'Kolom Nomor Telepon Harus Diisi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        } else {
            try {
                # Transaksi Start
                # untuk insert tenaga medis dan jadwal dokter
                DB::beginTransaction();
                if (empty($request->id)) {
                    # Jika kondisi insert tenaga medis baru tetapi nakes_id telah digunakan
                    $nakes = TenagaMedisTelemedicine::where('nakes_id', $request->nakes_id)->first();
                    if($nakes) {
                        DB::rollback();
                        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan, ID Tenaga Kesehatan telah digunakan'];
                    }
                    $data = new TenagaMedisTelemedicine;

                } else {
                    # Jika kondisi akan update tetapi data tidak ditemukan
                    $data = TenagaMedisTelemedicine::where('id_tenaga_medis', $request->id)->first();
                    if(!$data) {
                        DB::rollback();
                        return ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan, ID Tenaga Kesehatan tidak ditemukan'];
                    }
                }
                $data->nakes_id = $request->nakes_id;
                $data->no_telepon = $request->no_telepon;
                $data->poli_id = $request->poli_id;
                $data->jenis_nakes = $request->jenis_nakes;
                // $data->tarif = $request->tarif ? $request->tarif : 0;
                $data->save();
                $commit_status = true;

                # Jika tenaga medis yang insertkan jenis dokter
                # maka perlu insert jadwal di tabel jadwal_tenaga_medis
                # jika jenis tenaga medis perawat maka tidak perlu jadwal
                // if($request->jenis_nakes == 'dokter' && isset($request->jadwal)) {

                //     # Cari jadwal lama yang akan digantikan dan di delete
                //     # jadi tidak menggunakan Builder update, melainkan digantikan
                //     $deleteJadwal = JadwalTenagaMedis::where('nakes_id', $request->nakes_id)
                //         ->where('jenis_pelayanan', 'telemedicine')
                //         ->get();

                //     if($deleteJadwal) {

                //         # Jika jadwal lama ada maka dihapus
                //         if(count($deleteJadwal) > 0){
                //             if(!$deleteJadwal = JadwalTenagaMedis::where('nakes_id', $request->nakes_id)->where('jenis_pelayanan', 'telemedicine')->delete())
                //             {
                //                 # Jika gagal menghapus
                //                 $commit_status = false;
                //             }
                //         }
                //     }

                //     # Looping array jadwal yang baru dari request
                //     foreach ($request->jadwal as $key => $value) {

                //         # Insert jadwal baru
                //         $jadwal = new JadwalTenagaMedis();
                //         $jadwal->jam_awal = $value['awal'];
                //         $jadwal->jam_akhir = $value['akhir'];
                //         $jadwal->jenis_pelayanan = 'telemedicine';
                //         $jadwal->nakes_id = $request->nakes_id;
                //         $jadwal->save();

                //         # Jika gagal simpan
                //         if(!$jadwal) {
                //             $commit_status = false;
                //         }
                //     }

                // } else {
                //     # Jika jenis tenaga medis perawat, di cek jika memiliki jadwal
                //     $deleteJadwal = JadwalTenagaMedis::where('nakes_id', $request->nakes_id)
                //         ->where('jenis_pelayanan', 'telemedicine')
                //         ->get();
                //     if($deleteJadwal) {
                //         if(count($deleteJadwal) > 0){
                //             if(!$deleteJadwal = JadwalTenagaMedis::where('nakes_id', $request->nakes_id)->where('jenis_pelayanan', 'telemedicine')->delete())
                //             {
                //                 $commit_status = false;
                //             }
                //         }
                //     }
                // }

                if ($data && $commit_status) {
                    # commit transaksi dan response success
                    DB::commit();
                    $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
                } else {
                    # rollback transaksi dan response success
                    DB::rollback();
                    $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
                }
                # Transaksi End
                return $return;
            } catch (\Throwable $e) {
                # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
                $log = ['ERROR SIMPAN TENAGA MEDIS ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
                Help::logging($log);

                return Help::resApi('Terjadi kesalahan sistem',500);
            }
        }
    }

    public function formJadwal(Request $request) {
        $rules = array(
            'id' => 'required'
        );

        $messages = array(
            'id.required'  => 'Kolom Tenaga Kesetan Harus Diisi',
        );
        $valid = Validator::make($request->all(), $rules,$messages);
        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        }

        $data['tenaga_medis'] = TenagaMedisTelemedicine::where('id_tenaga_medis', $request->id)->first();
        if(!$data['tenaga_medis']) {
            return ['status' => 'error', 'code' => 201, 'message' => 'Tidak ditemukan tenaga medis yang dipilih'];
        }
        $data['jadwal_medis'] = JadwalTenagaMedis::where('nakes_id', $request->id)->get();

        $content = view('admin.telemedicine.tenagamedis.modalJadwal', $data)->render();
		return ['status' => 'success', 'content' => $content, 'data' => $data];
    }

    public function storeJadwal(Request $request) {
        $rules = array(
            'id' => 'required',
            'jadwal.*.hari' => 'required',
            'jadwal.*.awal' => 'required',
            'jadwal.*.akhir' => 'required',
        );

        $messages = array(
            'id.required'  => 'Kolom Tenaga Kesehatan Harus Diisi',
            'jadwal.*.hari.required'  => 'Kolom Hari Harus Diisi',
            'jadwal.*.awal.required'  => 'Kolom Jam Awal Harus Diisi',
            'jadwal.*.akhir.required'  => 'Kolom Jam Akhir Harus Diisi',
        );

        $valid = Validator::make($request->all(), $rules,$messages);

        if($valid->fails()) {
            return ['status' => 'error', 'code' => 400, 'message' => $valid->messages()];
        }

        DB::beginTransaction();

        try {

            $commit_status = true;

            # Cari jadwal lama yang akan digantikan dan di delete
            # jadi tidak menggunakan Builder update, melainkan digantikan
            $deleteJadwal = JadwalTenagaMedis::where('nakes_id', $request->id)
                ->where('jenis_pelayanan', 'telemedicine')
                ->get();

            if($deleteJadwal) {

                # Jika jadwal lama ada maka dihapus
                if(count($deleteJadwal) > 0){
                    if(!$deleteJadwal = JadwalTenagaMedis::where('nakes_id', $request->id)->where('jenis_pelayanan', 'telemedicine')->delete())
                    {
                        # Jika gagal menghapus
                        $commit_status = false;
                    }
                }
            }

            # Looping array jadwal yang baru dari request
            foreach ($request->jadwal as $key => $value) {

                # Insert jadwal baru
                $jadwal = new JadwalTenagaMedis();
                $jadwal->jam_awal = $value['awal'];
                $jadwal->jam_akhir = $value['akhir'];
                $jadwal->hari = $value['hari'];
                $jadwal->jenis_pelayanan = 'telemedicine';
                $jadwal->nakes_id = $request->id;
                $jadwal->save();

                # Jika gagal simpan
                if(!$jadwal) {
                    $commit_status = false;
                }
            }
            if ($commit_status) {
                # commit transaksi dan response success
                DB::commit();
                $return = ['code' => 200, 'type' => 'succes', 'status' => 'success', 'message' => 'Data Berhasil Di simpan'];
            } else {
                # rollback transaksi dan response success
                DB::rollback();
                $return = ['code' => 201, 'type' => 'error', 'status' => 'error', 'message' => 'Data Gagal Di simpan'];
            }
            # Transaksi End
            return $return;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SIMPAN JADWAL TENAGA MEDIS ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = TenagaMedisTelemedicine::where('id_tenaga_medis', $request->id)->first();
            $jadwal = JadwalTenagaMedis::where('nakes_id',$data->nakes_id)->where('jenis_pelayanan', 'telemedicine')->delete();
            $data->delete();

            if ($data) {
                $return = ['type' => 'success', 'status' => 'success', 'code' => '200'];
            } else {
                $return = ['type' => 'success', 'status' => 'success', 'code' => '201'];
            }
            return $return;
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR DELETE TENAGA MEDIS TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function getNakesTelemedicine(Request $request)
    {
        $data = UserRanap::select('id','name','level_user')->whereDoesntHave('tenaga_medis_telemedicine')->where('level_user', $request->jenis);
        $dataUpdate = UserRanap::select('id','name','level_user')->where('id',$request->selectedNakes)->where('level_user', $request->jenis)->union($data)->get();
        $data = $dataUpdate;
        // return $data;
        if (count($data)>0) {
            return Help::custom_response(200, "success", 'Ok', $data);
        }
        return Help::custom_response(500, "error", 'Data not found', null);
    }
}
