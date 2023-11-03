<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PermintaanTelemedicine;
use App\Models\TmCustomer;
use App\Models\ResepObat;
use App\Models\RekapMedik;
use App\Models\ResepObatDetail;
use App\Models\DBSIRAMA\MsItem;
use Illuminate\Http\Request;
use App\Helpers\Helpers as Help;

use Validator, DB, Config;

class ApiPelayananDokterController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getPermintaanTelemedicine(Request $request) {
        $validate = Validator::make($request->all(),[
            'tenaga_medis_id' => 'required'
        ],[
            'tenaga_medis_id.required' => 'Tenaga Medis Wajib Di isi'
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
            $dateNow = date('Y-m-d');
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'permintaan_telemedicine.nama', 'permintaan_telemedicine.no_rm', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'keluhan')
                ->where('permintaan_telemedicine.tenaga_medis_id', $request->tenaga_medis_id)
                ->where('status_pasien', 'proses')
                ->where('tanggal_kunjungan', '>=', $dateNow)
                ->orderBy('tanggal_kunjungan', 'asc')
                ->get();
            if (count($permintaan)>0) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Data List Permintaan Layanan berhasil ditemukan'
                    ],
                    'response' => $permintaan
                ];
            } else {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Data List Permintaan Layanan tidak ditemukan'
                    ],
                    'response' => []
                ];
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET LIST PERMINTAAN TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getRiwayatTelemedicine(Request $request) {
        $validate = Validator::make($request->all(),[
            'tenaga_medis_id' => 'required'
        ],[
            'tenaga_medis_id.required' => 'Tenaga Medis Wajib Di isi'
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
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'permintaan_telemedicine.nama', 'permintaan_telemedicine.no_rm', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'jenis_kelamin', DB::raw('DATEDIFF(tanggal_kunjungan, tanggal_lahir) as umur'))
                ->where('permintaan_telemedicine.tenaga_medis_id', $request->tenaga_medis_id)
                ->where('status_pasien', 'selesai')
                ->orderBy('tanggal_kunjungan', 'asc')
                ->get();
            if (count($permintaan)>0) {
                return [
                    'metaData' => [
                        "code" => 200,
                        "message" => 'Data Riwayat Permintaan berhasil ditemukan'
                    ],
                    'response' => $permintaan
                ];
            } else {
                return [
                    'metaData' => [
                        "code" => 204,
                        "message" => 'Data Riwayat Permintaan tidak ditemukan'
                    ],
                    'response' => []
                ];
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET LIST RIWAYAT TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
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
                ->with('resep_obat', function($q) {
                    $q->with('resep_obat_detail');
                })
                ->with('rekap_medik')
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
            'detail_obat.*.id_obat' => 'required',
            'detail_obat.*.qty' => 'required',
            'detail_obat.*.signa' => 'required',
            'anamnesis' => 'required',
            'pemeriksaan_fisik' => 'required',
            'assessment' => 'required',
            'rencana_dan_terapi' => 'required',
            'id_dokter' => 'required'
        ],[
            'id_permintaan_telemedicine.required' => 'Id Permintaan Telemedicine Wajib Di isi',
            'detail_obat.*.id_obat.required' => 'Kolom Obat Harus Diisi',
            'detail_obat.*.qty.required' => 'Kolom Quantity Harus Diisi',
            'detail_obat.*.signa.required' => 'Kolom Signa Harus Diisi',
            'anamnesis.required' => 'Anamnesis Wajib Di isi',
            'pemeriksaan_fisik.required' => 'Pemeriksaan Fisik Wajib Di isi',
            'assessment.required' => 'Assessment Wajib Di isi',
            'rencana_dan_terapi.required' => 'Rencana dan Terapi Wajib Di isi',
            'id_dokter.required' => 'ID Dokter Wajib Di isi'
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

        # untuk insert SOAP di tabel rekap_medik
        # dan insert detail resep obat di tabel resep_obat_detail
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

            # Cari jika soap sudah pernah di kerjakan oleh dokter di tabel rekap_medik
            if (!$rekap_medik = RekapMedik::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan','telemedicine')->first()) {
                # Jika belum di proses, maka akan dibuat model rekap medik baru
                $rekap_medik = new RekapMedik;
                $rekap_medik->permintaan_id = $request->id_permintaan_telemedicine;
                $rekap_medik->jenis_layanan = 'telemedicine';
            }

            # Jika sudah di proses, maka update soapnya di tabel rekap medik
            $rekap_medik->dokter_id = $request->id_dokter;
            $rekap_medik->anamnesis = isset($request->anamnesis) ? $request->anamnesis : null;
            $rekap_medik->pemeriksaan_fisik = isset($request->pemeriksaan_fisik) ? $request->pemeriksaan_fisik : null;
            $rekap_medik->assessment = isset($request->assessment) ? $request->assessment : null;
            $rekap_medik->rencana_dan_terapi = isset($request->rencana_dan_terapi) ? $request->rencana_dan_terapi : null;
            $rekap_medik->save();

            # Jika gagal simpan SOAP ke tabel resep_obat
            if (!$rekap_medik) {
                DB::rollback();
                return [
                    'metaData' => [
                        "code" => 400,
                        "message" => 'Gagal menyimpan SOAP'
                    ],
                    'response' => []
                ];

            }

            # Cari jika permintaan telemedis nya sudah/belum diproses resep sebelumnya
            if(!$resep = ResepObat::where('permintaan_id', $request->id_permintaan_telemedicine)->first()) {
                # Jika belum di proses, maka akan dibuat model resep baru
                $resep = new ResepObat;
                $resep->permintaan_id = $request->id_permintaan_telemedicine;

                # Jika soap dan resep baru, maka digenerate nomor resep
                $resep->no_resep = Help::generateNoResepTelemedicine();
            }
            $resep->tgl_resep = date('Y-m-d H:i:s');
            $resep->berlaku_sampai = date('Y-m-d H:i:s', strtotime('+2 day'));
            $resep->jenis_layanan = 'telemedicine';
            $resep->save();

            // DB::rollback();
            // return $resep;

            # Jika gagal simpan SOAP ke tabel resep_obat
            if (!$resep) {
                DB::rollback();
                return [
                    'metaData' => [
                        "code" => 400,
                        "message" => 'Gagal menyimpan Resep'
                    ],
                    'response' => []
                ];

            }

            # Jika berhasil simpan resep, Lanjut simpan detail obat
            # Tapi hapus obat lama terlebih dahulu
            $old_detail_obat = ResepObatDetail::where('resep_obat_id', $resep->id_resep_obat)->delete();

            # Start proses insert obat array
            if(isset($request->detail_obat)) {
                foreach ($request->detail_obat as $k => $v) {
                    $detail_obat = new ResepObatDetail;
                    $detail_obat->id_obat = $v['id_obat'];
                    $detail_obat->qty = $v['qty'];
                    $detail_obat->signa = $v['signa'];
                    $detail_obat->resep_obat_id = $resep->id_resep_obat;
                    $detail_obat->save();
                    if(!$detail_obat) {
                        DB::rollback();
                        return [
                            'metaData' => [
                                "code" => 400,
                                "message" => 'Gagal Menyimpan detail obat'
                            ],
                            'response' => []
                        ];
                    }
                }
            }
            DB::commit();
            // DB::rollback();
            return [
                'metaData' => [
                    "code" => 200,
                    "message" => 'Resep Berhasil Disimpan'
                ],
                'response' => []
            ];

        } catch (\Throwable $e) {
            DB::rollback();
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SAVE RESEP TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function cariObat($q) {
        return MsItem::select('ms_item.item_id','item_name','item_unitofitem as satuan_item','item_code')
            ->has('Price')
            ->with('Price:item_id,price_sell_after_margin as harga')
            ->join('farmasi.price as p', 'ms_item.item_id', 'p.item_id')
            ->limit(10)->where('item_name', 'like', '%'.$q.'%')->get();
    }
}
