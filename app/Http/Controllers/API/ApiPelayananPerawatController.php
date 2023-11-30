<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PermintaanTelemedicine;
use App\Models\PermintaanHC;
use App\Models\RekamMedisLanjutan;
use App\Models\VideoConference;
use App\Models\PengaturanHC;
use App\Models\TmCustomer;
use App\Models\PaymentPermintaan;
use App\Models\ResepObat;
use App\Models\Rating;
use App\Models\Activity;
use App\Models\ResepObatDetail;
use App\Models\DBSIRAMA\MsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return  Help::resApi($validate->errors()->all()[0],400);
        }

        $sekarang = date('Y-m-d');

        try{
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'nama', 'no_rm', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien')
                ->where('status_pasien', 'proses')
                ->whereDoesntHave('rekam_medis_lanjutan')
                ->whereHas('video_conference')
                ->with('video_conference')
                ->where('tanggal_kunjungan', '>=', $sekarang)
                ->where('perawat_id', $request->perawat_id)
                ->orderBy('tanggal_kunjungan', 'asc')
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
                return Help::resApi('Berhasil',200,$permintaan);
            } else {
                return Help::resApi('Data Permintaan Tidak ditemukan',204);
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
            return Help::resApi($validate->errors()->all()[0],400);
        }

        $sekarang = date('Y-m-d H:i:s');

        try{
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'nama', 'no_rm', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien')
                ->whereIn('status_pasien', ['proses','selesai'])
                ->where('perawat_id', $request->perawat_id)
                ->whereHas('video_conference', function($q) use($sekarang) {
                    $q->where('waktu_selesai', '<', $sekarang);
                })
                // ->whereHas('rekam_medis_lanjutan')
                ->get();
                if (count($permintaan)>0) {
                return Help::resApi('Berhasil',200,$permintaan);
            } else {
                return Help::resApi('Data Permintaan Tidak ditemukan',204);
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
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try{
            # Permintaan form berdasarkan data permintaan_id
            # dan jika resep sudah pernah di buat, ambil resepnya biar bisa di edit
            $data = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'permintaan_telemedicine.nama', 'permintaan_telemedicine.no_rm', 'tanggal_kunjungan')
                ->where('permintaan_telemedicine.id_permintaan_telemedicine', $request->id_permintaan_telemedicine)
                ->with('rekam_medis_lanjutan')
                ->where('status_pasien', 'proses')
                ->first();
            if ($data) {
                return Help::resApi('Permintaan ditemukan',200,$data);
            } else {
                return Help::resApi('Permintaan tidak ditemukan',204);
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET FORM RESEP OBAT TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function formResepHomecare(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_permintaan_hc' => 'required'
        ],[
            'id_permintaan_hc.required' => 'Id Permintaan Homecare Wajib Di isi'
        ]);

        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try{
            # Permintaan form berdasarkan data permintaan_id
            # dan jika resep sudah pernah di buat, ambil resepnya biar bisa di edit
            $data = PermintaanHC::select('id_permintaan_hc', 'permintaan_hc.nama', 'permintaan_hc.no_rm', 'tanggal_kunjungan')
                ->where('permintaan_hc.id_permintaan_hc', $request->id_permintaan_hc)
                ->with('rekam_medis_lanjutan')
                ->where('status_pasien', 'proses')
                ->first();
            if ($data) {
                return Help::resApi('Permintaan ditemukan',200,$data);
            } else {
                return Help::resApi('Permintaan tidak ditemukan',204);
            }

        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET FORM RESEP OBAT HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
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
            return Help::resApi($validate->errors()->all()[0],400);
        }
        # Transaksi Start
        DB::beginTransaction();

        # untuk insert SOAP di tabel rekam_medis_lanjutan
        try{

            # Cari permintaan telemedis yang akan di proses resep
            if(!$permintaan = PermintaanTelemedicine::where('permintaan_telemedicine.id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->first()) {
                DB::rollback();
                return Help::resApi('Tidak ditemukan permintaan',204);
            }

            # Tidak bisa melakukan peresepan ketika tidak ada layanan
            $jadwal_dokter = explode('-',$permintaan->jadwal_dokter);
            if(date('Y-m-d H:i:s', strtotime($permintaan->tanggal_kunjungan.$jadwal_dokter[0])) > date('Y-m-d H:i:s')) {
                DB::rollback();
                return Help::resApi('Pelayanan belum dimulai',400);
            }

            # Cek layanan vicon harus ada
            if (!$vicon = VideoConference::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan', 'telemedicine')->first()){
                DB::rollback();
                return Help::resApi('Layanan meeting tidak ditemukan',204);
            }

            if($vicon->waktu_mulai == "") {
                DB::rollback();
                return Help::resApi('Gagal menyimpan, Layanan meeting belum dilaksanakan',400);
            }

            # Cari jika soap sudah pernah di kerjakan oleh perawat di tabel rekam_medis_lanjutan
            if ($rekam_medis_lanjutan = RekamMedisLanjutan::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan','telemedicine')->first()) {
                # Jika sudah di proses, maka update soapnya tidak bisa diupdate
                DB::rollback();
                return Help::resApi('Gagal menyimpan, Tidak bisa mengupdate soap yang sudah tersimpan',400);

            }

            # Jika belum di proses, maka akan dibuat model rekam medis lanjutan baru
            $rekam_medis_lanjutan = new RekamMedisLanjutan;
            $rekam_medis_lanjutan->permintaan_id = $request->id_permintaan_telemedicine;
            $rekam_medis_lanjutan->jenis_layanan = 'telemedicine';
            $rekam_medis_lanjutan->perawat_id = $request->id_perawat;
            $rekam_medis_lanjutan->anamnesis = isset($request->anamnesis) ? $request->anamnesis : null;
            $rekam_medis_lanjutan->pemeriksaan_fisik = isset($request->pemeriksaan_fisik) ? $request->pemeriksaan_fisik : null;
            $rekam_medis_lanjutan->assessment = isset($request->assessment) ? $request->assessment : null;
            $rekam_medis_lanjutan->rencana_dan_terapi = isset($request->rencana_dan_terapi) ? $request->rencana_dan_terapi : null;

            # Jika gagal simpan SOAP ke tabel resep_obat
            if (!$rekam_medis_lanjutan->save()) {
                DB::rollback();
                return Help::resApi('Gagal menyimpan SOAP',400);
            }


            # Akhiri layanan vicon jika belum diakhiri
            # Sebelumnya saat vicon dimulai, waktu mulai dan waktu selesai sudah di set
            $sekarang = date('Y-m-d H:i:s');
            if(date('Y-m-d H:i:s', strtotime($vicon->waktu_selesai)) > $sekarang){
                $vicon->waktu_selesai = $sekarang;
                if(!$vicon->save()){
                    DB::rollback();
                    return Help::resApi('Gagal mengakhiri layanan meeting',400);
                }
            }

            if(!$activity = Activity::store(Auth::user()->id,'Perawat simpan soap telemedicine')) {
                DB::rollback();
                return Help::resApi('Gagal menyimpan soap telemedicine',400);
            }

            DB::commit();
            // DB::rollback();
            return Help::resApi('SOAP Berhasil Disimpan',200);

        } catch (\Throwable $e) {
            DB::rollback();
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SAVE SOAP TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function saveResepHomecare(Request $request) {

        $validate = Validator::make($request->all(),[
            'id_permintaan_hc' => 'required',
            // 'detail_obat.*.id_obat' => 'required',
            // 'detail_obat.*.qty' => 'required',
            // 'detail_obat.*.signa' => 'required',
            // 'detail_obat.*.nama_obat' => 'required',
            // 'detail_obat.*.kode_obat' => 'required',
            // 'detail_obat.*.harga' => 'required',
            'anamnesis' => 'required',
            'pemeriksaan_fisik' => 'required',
            'assessment' => 'required',
            'rencana_dan_terapi' => 'required',
            'id_perawat' => 'required'
        ],[
            'id_permintaan_hc.required' => 'Id Permintaan Homecare Wajib Di isi',
            // 'detail_obat.*.id_obat.required' => 'Kolom Obat Harus Diisi',
            // 'detail_obat.*.qty.required' => 'Kolom Quantity Harus Diisi',
            // 'detail_obat.*.signa.required' => 'Kolom Signa Harus Diisi',
            // 'detail_obat.*.nama_obat.required' => 'Kolom Nama Obat Harus Diisi',
            // 'detail_obat.*.kode_obat.required' => 'Kolom Kode Obat Harus Diisi',
            // 'detail_obat.*.harga.required' => 'Kolom Harga Harus Diisi',
            'anamnesis.required' => 'Anamnesis Wajib Di isi',
            'pemeriksaan_fisik.required' => 'Pemeriksaan Fisik Wajib Di isi',
            'assessment.required' => 'Assessment Wajib Di isi',
            'rencana_dan_terapi.required' => 'Rencana dan Terapi Wajib Di isi',
            'id_perawat.required' => 'ID Perawat Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        # Transaksi Start
        DB::beginTransaction();

        # input datail rekam_medis_lanjutan oleh perawat
        ########## CANCEL # dan insert keterangan resep ke resep_obat
        ########## CANCEL # dan insert detail resep obat di tabel resep_obat_detail
        try{

            # Cari permintaan homecare yang akan di proses resep
            if(!$permintaan = PermintaanHC::where('permintaan_hc.id_permintaan_hc', $request->id_permintaan_hc)->first()) {
                DB::rollback();
                return Help::resApi('Tidak ditemukan permintaan',204);
            }

            # Cari jika soap sudah pernah di kerjakan oleh perawat di tabel rekam_medis_lanjutan
            # Tidak bisa mengubah data resep yang sudah di input
            if ($rekam_medis_lanjutan = RekamMedisLanjutan::where('permintaan_id', $request->id_permintaan_hc)->where('jenis_layanan','homecare')->first()) {
                DB::rollback();
                return Help::resApi('Gagal menyimpan, Tidak bisa mengupdate resep yang sudah tersimpan',400);
            }

            # Jika belum di proses, maka akan dibuat model rekap medik baru
            $rekam_medis_lanjutan = new RekamMedisLanjutan;
            $rekam_medis_lanjutan->permintaan_id = $request->id_permintaan_hc;
            $rekam_medis_lanjutan->jenis_layanan = 'homecare';
            # Jika sudah di proses, maka update soapnya di tabel rekap medik
            $rekam_medis_lanjutan->perawat_id = $request->id_perawat;
            $rekam_medis_lanjutan->anamnesis = isset($request->anamnesis) ? $request->anamnesis : null;
            $rekam_medis_lanjutan->pemeriksaan_fisik = isset($request->pemeriksaan_fisik) ? $request->pemeriksaan_fisik : null;
            $rekam_medis_lanjutan->assessment = isset($request->assessment) ? $request->assessment : null;
            $rekam_medis_lanjutan->rencana_dan_terapi = isset($request->rencana_dan_terapi) ? $request->rencana_dan_terapi : null;

            # Jika gagal simpan SOAP ke tabel resep_obat
            if (!$rekam_medis_lanjutan->save()) {
                DB::rollback();
                return Help::resApi('Gagal menyimpan SOAP',400);
            }

            ########## CANCEL
            // $total = 0;
            // if(isset($request->detail_obat)) {
            if(1==2) {
                foreach ($request->detail_obat as $k => $v) {
                    $total += (float)$v['harga'] * (integer)$v['qty'];
                }
                if(!$pengaturan = PengaturanHC::first()){
                    DB::rollback();
                    return Help::resApi('Terjadi kesalahan sistem, Error pengaturan telemedicine, silahkan hubungi admin',400);
                }

                $payment = new PaymentPermintaan;
                $payment->permintaan_id = $request->id_permintaan_hc;
                $payment->nominal = $total;
                $payment->ongkos_kirim = (float)$pengaturan->biaya_per_km * ceil($permintaan->jarak);
                $payment->jenis_layanan = 'eresep_homecare';
                $payment->tgl_expired = date('Y-m-d H:i:s', strtotime('+30 minute'));
                $payment->status = 'UNCONFIRMED';
                if (!$payment->save()) {
                    DB::rollback();
                    return Help::resApi('Gagal saat menyimpan data invoice dari obat yang disimpan',400);
                }

                # Cari jika permintaan homecare nya sudah/belum diproses resep sebelumnya
                if($resep = ResepObat::where('permintaan_id', $request->id_permintaan_hc)->where('jenis_layanan','homecare')->first()) {
                    DB::rollback();
                    return Help::resApi('Tidak bisa merubah resep yang sudah tersimpan sebelumnya',400);
                }

                # Jika belum di proses, maka akan dibuat model resep baru
                $resep = new ResepObat;
                $resep->permintaan_id = $request->id_permintaan_hc;

                # Jika soap dan resep baru, maka digenerate nomor resep
                $resep->no_resep = Help::generateNoResepHc();
                $resep->status_pembayaran = 'belum';

                # Jika sistemnya bisa update resep, maka perlu di cek apakah resep sebelumnya sudah dilunasi
                if($resep->status_pembayaran != 'belum'){
                    DB::rollback();
                    return Help::resApi('Gagal memperbarui resep, status pembayaran tidak valid',400);
                }

                $resep->total_bayar = $total;
                $resep->tgl_resep = date('Y-m-d H:i:s');
                $resep->berlaku_sampai = date('Y-m-d H:i:s', strtotime('+2 day'));
                $resep->jenis_layanan = 'homecare';

                // DB::rollback();
                // return $resep;

                # Jika gagal simpan SOAP ke tabel resep_obat
                if (!$resep->save()) {
                    DB::rollback();
                    return Help::resApi('Gagal menyimpan Resep',400);
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
                        $detail_obat->nama_obat = $v['nama_obat'];
                        $detail_obat->kode_obat = $v['kode_obat'];
                        $detail_obat->harga = $v['harga'];
                        $detail_obat->resep_obat_id = $resep->id_resep_obat;
                        $detail_obat->save();
                        if(!$detail_obat) {
                            DB::rollback();
                            return Help::resApi('Gagal Menyimpan detail obat',400);
                        }
                    }
                }
            }

            if(!$activity = Activity::store(Auth::user()->id,'Perawat simpan soap homecare')) {
                DB::rollback();
                return Help::resApi('Gagal simpan soap homecare',400);
            }

            DB::commit();
            // DB::rollback();
            return Help::resApi('Soap Berhasil Disimpan',200);

        } catch (\Throwable $e) {
            DB::rollback();
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SAVE RESEP HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function layaniTelemedicine(Request $request) {
        $validate = Validator::make($request->all(),[
            'id_video_conference' => 'required'
        ],[
            'id_video_conference.required' => 'Id Video Conference Wajib Di isi'
        ]);

        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        DB::beginTransaction();

        try {
            if(!$vicon = VideoConference::where('id_video_conference', $request->id_video_conference)->where('jenis_layanan', 'telemedicine')->with('permintaan_telemedicine')->first()){
                return Help::resApi('Tidak ditemukan link pelayanan',204);
            }
            $sekarang = date('Y-m-d H:i:s');
            $jadwal_dokter = date('Y-m-d H:i:s', strtotime($vicon->permintaan_telemedicine->tanggal_kunjungan.$vicon->permintaan_telemedicine->jadwal_dokter));
            if($jadwal_dokter>$sekarang){
                return Help::resApi('Jam layanan belum di mulai', 400);
            }
            if($vicon->waktu_selesai != ""){
                if($sekarang > date('Y-m-d H:i:s', strtotime($vicon->waktu_selesai))){
                    return Help::resApi('Jam layanan sudah diakhiri', 400);
                }
            }
            if($vicon->waktu_mulai == ""){
                $vicon->waktu_mulai = date('Y-m-d H:i:s');
                $vicon->waktu_selesai = date('Y-m-d 23:59');
                if(!$vicon->save()){
                    return Help::resApi('Gagal memulai layanan', 400);
                }
                if(!$activity = Activity::store(Auth::user()->id,'Perawat layanan telemedicine dimulai')) {
                    DB::rollback();
                    return Help::resApi('Gagal memulai layanan telemedicine',400);
                }
            }
            DB::commit();
            return Help::resApi('Layanan dimulai', 200, $vicon->link_vicon);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET FORM RESEP OBAT TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function cariObat($q) {
        try {
            $obat = MsItem::select('ms_item.item_id','item_name','item_unitofitem as satuan_item','item_code')
                ->has('Price')
                ->with('Price:item_id,price_sell_after_margin as harga')
                ->join('farmasi.price as p', 'ms_item.item_id', 'p.item_id')
                ->limit(10)->where('item_name', 'like', '%'.$q.'%')->get();
            return Help::resApi('Berhasil', 200, $obat);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET FORM RESEP OBAT HOMECARE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getPenilaian(Request $request) {
        $validate = Validator::make($request->all(),[
            'perawat_id' => 'required'
        ],[
            'perawat_id.required' => 'Id Perawat Wajib Di isi'
        ]);

        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try {
            $penilaianTele = Rating::has('permintaan_telemedicine')
                ->whereHas('permintaan_telemedicine',function($q) use($request) {
                    $q->select('id_permintaan_telemedicine','nama')->where('perawat_id',$request->perawat_id);
                })
                ->with('permintaan_telemedicine',function($q) use($request) {
                    $q->select('id_permintaan_telemedicine','nama')->where('perawat_id',$request->perawat_id);
                })
                ->where('jenis_layanan','telemedicine');
            // $penilaianHc = Rating::whereHas('permintaan_hc:id_permintaan_hc,nama');
            // $penilaianMcu = Rating::with('permintaan_mcu:id_permintaan,nama');
            // $penilaian = $penilaianTele->union($penilaianHc)->union($penilaianMcu)->get();
            $penilaian = $penilaianTele->get();
            return Help::resApi('Berhasil', 200, $penilaian);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET PENILAIAN PERAWAT TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
