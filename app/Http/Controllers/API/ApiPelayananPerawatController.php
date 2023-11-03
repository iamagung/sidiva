<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PermintaanTelemedicine;
use App\Models\PermintaanHC;
use App\Models\RekamMedisLanjutan;
use App\Models\TmCustomer;
use Illuminate\Http\Request;
use App\Helpers\Helpers as Help;

use Validator, DB;

class ApiPelayananPerawatController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getPerawatPelayanan(Request $request) {
        $validate = Validator::make($request->all(),[
            'perawat_id' => 'required'
        ],[
            'perawat_id.required' => 'Perawat ID Wajib Di isi'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 400,
                ],
                'response' => [],
            ]);
        }

        $sekarang = date('Y-m-d');

        try{
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'nama', 'no_rm', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien')
                ->where('status_pasien', 'proses')
                ->where('tanggal_kunjungan', $sekarang)
                ->where('perawat_id', $request->perawat_id)
                ->get();
            // $permintaan_telemedicine = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'permintaan_telemedicine.nama', 'tm_customer.KodeCust as no_rm', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', DB::raw("'tm' as layanan"))
            //     ->join(DB::connection('dbrsud')->raw('dbsimars_baru.tm_customer'),'permintaan_telemedicine.nik','=','tm_customer.NoKtp')
            //     ->join('users_android as ua', 'permintaan_telemedicine.nik', '=', 'ua.nik')
            //     ->where('perawat_id', '=', $id_tenaga_medis)
            //     ->where('tanggal_kunjungan', '=', $sekarang)
            //     ->where('status_pasien', '=', 'proses')
            //     ->get();
            // $permintaan_hc = PermintaanHC::select('id_permintaan_hc', 'permintaan_hc.nama', 'tm_customer.KodeCust as no_rm', 'tanggal_kunjungan', 'status_pasien', DB::raw("'hc' as layanan, null as jadwal_dokter"))
            //     ->join(DB::connection('dbrsud')->raw('dbsimars_baru.tm_customer'),'permintaan_hc.nik','=','tm_customer.NoKtp')
            //     ->join('layanan_hc', 'permintaan_hc.layanan_hc_id', '=', 'layanan_hc.id_layanan_hc')
            //     ->join('users_android as ua', 'permintaan_hc.nik', '=', 'ua.nik')
            //     // ->where('perawat_id', '=', $id_tenaga_medis)
            //     ->where('tanggal_kunjungan', '=', $sekarang)
            //     ->where('status_pasien', '=', 'proses')
            //     ->get();
            // $permintaan = $permintaan_hc->union($permintaan_telemedicine);
            // $permintaan = $permintaan_telemedicine;
            if (count($permintaan)>0) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $permintaan
                ];
            } else {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Data Permintaan Tidak ditemukan'
                    ],
                    'response' => []
                ];
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET LIST POLI TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }

    }

    public function getRiwayatPelayanan(Request $request) {
        $validate = Validator::make($request->all(),[
            'perawat_id' => 'required'
        ],[
            'perawat_id.required' => 'Perawat ID Wajib Di isi'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 400,
                ],
                'response' => [],
            ]);
        }

        $sekarang = date('Y-m-d');

        try{
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'nama', 'no_rm', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien')
                ->where('status_pasien', 'proses')
                ->where('tanggal_kunjungan', $sekarang)
                ->where('perawat_id', $request->perawat_id)
                ->get();
            if (count($permintaan)>0) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Berhasil'
                    ],
                    'response' => $permintaan
                ];
            } else {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Data Permintaan Tidak ditemukan'
                    ],
                    'response' => []
                ];
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET LIST POLI TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }

    }

    public function formResepTelemedicine(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required'
        ],[
            'id_permintaan_telemedicine.required' => 'Id Permintaan Telemedicine Wajib Di isi'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 400,
                ],
                'response' => [],
            ]);
        }

        try{
            # Permintaan form berdasarkan data permintaan_id
            # dan jika resep sudah pernah di buat, ambil resepnya biar bisa di edit
            $data = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'permintaan_telemedicine.nama', 'permintaan_telemedicine.no_rm', 'tanggal_kunjungan')
                ->where('permintaan_telemedicine.id_permintaan_telemedicine', $request->id_permintaan_telemedicine)
                ->with('rekam_medis_lanjutan')
                ->where('status', 'proses')
                ->first();
            if ($data) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Permintaan ditemukan'
                    ],
                    'response' => $data
                ];
            } else {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Permintaan tidak ditemukan'
                    ],
                    'response' => []
                ];
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET FORM RESEP OBAT TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function saveResepTelemedicine(Request $request) {

        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required',
            'anamnesis' => 'required',
            'pemeriksaan_fisik' => 'required',
            'assessment' => 'required',
            'rencana_dan_terapi' => 'required',
            'id_perawat' => 'required'
        ],[
            'id_permintaan_telemedicine.required' => 'Id Permintaan Telemedicine Wajib Di isi',
            'anamnesis.required' => 'Anamnesis Wajib Di isi',
            'pemeriksaan_fisik.required' => 'Pemeriksaan Fisik Wajib Di isi',
            'assessment.required' => 'Assessment Wajib Di isi',
            'rencana_dan_terapi.required' => 'Rencana dan Terapi Wajib Di isi',
            'id_perawat.required' => 'ID Perawat Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'metadata' => [
                    'message' => $validate->errors()->all()[0],
                    'code'    => 400,
                ],
                'response' => [],
            ]);
        }
        # Transaksi Start
        DB::beginTransaction();

        # untuk insert SOAP di tabel rekam_medis_lanjutan
        try{

            # Cari permintaan telemedis yang akan di proses resep
            if(!$permintaan = PermintaanTelemedicine::where('permintaan_telemedicine.id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->first()) {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Tidak ditemukan permintaan'
                    ],
                    'response' => []
                ];
            }

            # Cari jika soap sudah pernah di kerjakan oleh perawat di tabel rekam_medis_lanjutan
            if (!$rekam_medis_lanjutan = RekamMedisLanjutan::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan','telemedicine')->first()) {
                # Jika belum di proses, maka akan dibuat model rekam medis lanjutan baru
                $rekam_medis_lanjutan = new RekamMedisLanjutan;
                $rekam_medis_lanjutan->permintaan_id = $request->id_permintaan_telemedicine;
                $rekam_medis_lanjutan->jenis_layanan = 'telemedicine';
            }

            # Jika sudah di proses, maka update soapnya di tabel rekam_medis_lanjutan
            $rekam_medis_lanjutan->perawat_id = $request->id_perawat;
            $rekam_medis_lanjutan->anamnesis = isset($request->anamnesis) ? $request->anamnesis : null;
            $rekam_medis_lanjutan->pemeriksaan_fisik = isset($request->pemeriksaan_fisik) ? $request->pemeriksaan_fisik : null;
            $rekam_medis_lanjutan->assessment = isset($request->assessment) ? $request->assessment : null;
            $rekam_medis_lanjutan->rencana_dan_terapi = isset($request->rencana_dan_terapi) ? $request->rencana_dan_terapi : null;
            $rekam_medis_lanjutan->save();

            # Jika gagal simpan SOAP ke tabel resep_obat
            if (!$rekam_medis_lanjutan) {
                DB::rollback();
                return [
                    'metaData' => [
                        "code" => 400,
                        "message" => 'Gagal menyimpan SOAP'
                    ],
                    'response' => []
                ];

            }

            DB::commit();
            // DB::rollback();
            return [
                'metaData' => [
                    "code" => 200,
                    "message" => 'SOAP Berhasil Disimpan'
                ],
                'response' => []
            ];

        } catch (\Throwable $e) {
            DB::rollback();
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SAVE SOAP TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
