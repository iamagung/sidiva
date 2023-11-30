<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PermintaanTelemedicine;
use App\Models\TmCustomer;
use App\Models\ResepObat;
use App\Models\RekapMedik;
use App\Models\ResepObatDetail;
use App\Models\VideoConference;
use App\Models\PaymentPermintaan;
use App\Models\PengaturanTelemedicine;
use App\Models\Rating;
use App\Models\Activity;
use App\Models\DBSIRAMA\MsItem;
use Illuminate\Http\Request;
use App\Helpers\Helpers as Help;
use Illuminate\Support\Facades\Auth;

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
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try{
            $dateNow = date('Y-m-d');
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'permintaan_telemedicine.nama', 'permintaan_telemedicine.no_rm', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'keluhan')
                ->where('permintaan_telemedicine.tenaga_medis_id', $request->tenaga_medis_id)
                ->where('status_pasien', 'proses')
                ->with('video_conference')
                ->where('tanggal_kunjungan', '>=', $dateNow)
                ->whereDoesntHave('rekap_medik')
                ->orderBy('tanggal_kunjungan', 'asc')
                ->get();
            if (count($permintaan)>0) {
                return Help::resApi('Data List Permintaan Layanan berhasil ditemukan',200,$permintaan);
            } else {
                return Help::resApi('Data List Permintaan Layanan tidak ditemukan',204);
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
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try{
            $sekarang = date('Y-m-d H:i:s');
            $permintaan = PermintaanTelemedicine::select('id_permintaan_telemedicine', 'permintaan_telemedicine.nama', 'permintaan_telemedicine.no_rm', 'tanggal_kunjungan', 'jadwal_dokter', 'status_pasien', 'jenis_kelamin', DB::raw('DATEDIFF(tanggal_kunjungan, tanggal_lahir) as umur'))
                ->where('permintaan_telemedicine.tenaga_medis_id', $request->tenaga_medis_id)
                ->whereIn('status_pasien', ['proses','selesai'])
                ->whereHas('video_conference', function($q) use($sekarang) {
                    $q->where('waktu_selesai', '<', $sekarang);
                })
                // ->whereHas('rekap_medik')
                ->orderBy('tanggal_kunjungan', 'asc')
                ->get();
            if (count($permintaan)>0) {
                return Help::resApi('Data Riwayat Permintaan berhasil ditemukan',200,$permintaan);
            } else {
                return Help::resApi('Data Riwayat Permintaan tidak ditemukan',204);
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
            return Help::resApi($validate->errors()->all()[0],400);
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

    public function saveResepTelemedicine(Request $request) {

        $validate = Validator::make($request->all(),[
            'id_permintaan_telemedicine' => 'required',
            'detail_obat.*.id_obat' => 'required',
            'detail_obat.*.qty' => 'required',
            'detail_obat.*.signa' => 'required',
            'detail_obat.*.nama_obat' => 'required',
            'detail_obat.*.kode_obat' => 'required',
            'detail_obat.*.harga' => 'required',
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
            'detail_obat.*.nama_obat.required' => 'Kolom Nama Obat Harus Diisi',
            'detail_obat.*.kode_obat.required' => 'Kolom Kode Obat Harus Diisi',
            'detail_obat.*.harga.required' => 'Kolom Harga Harus Diisi',
            'anamnesis.required' => 'Anamnesis Wajib Di isi',
            'pemeriksaan_fisik.required' => 'Pemeriksaan Fisik Wajib Di isi',
            'assessment.required' => 'Assessment Wajib Di isi',
            'rencana_dan_terapi.required' => 'Rencana dan Terapi Wajib Di isi',
            'id_dokter.required' => 'ID Dokter Wajib Di isi'
        ]);
        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        # Transaksi Start
        DB::beginTransaction();

        # untuk insert SOAP di tabel rekap_medik
        # dan insert detail resep obat di tabel resep_obat_detail
        try{

            # Cari permintaan telemedis yang akan di proses resep
            if(!$permintaan = PermintaanTelemedicine::where('permintaan_telemedicine.id_permintaan_telemedicine', $request->id_permintaan_telemedicine)->first()) {
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

            # Cari jika soap sudah pernah di kerjakan oleh dokter di tabel rekap_medik
            # Tidak bisa mengubah data resep yang sudah di input
            if ($rekap_medik = RekapMedik::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan','telemedicine')->first()) {
                DB::rollback();
                return Help::resApi('Gagal menyimpan, Tidak bisa mengupdate resep yang sudah tersimpan',400);
            }

            # Jika belum di proses, maka akan dibuat model rekap medik baru
            $rekap_medik = new RekapMedik;
            $rekap_medik->permintaan_id = $request->id_permintaan_telemedicine;
            $rekap_medik->jenis_layanan = 'telemedicine';
            # Jika sudah di proses, maka update soapnya di tabel rekap medik
            $rekap_medik->dokter_id = $request->id_dokter;
            $rekap_medik->anamnesis = isset($request->anamnesis) ? $request->anamnesis : null;
            $rekap_medik->pemeriksaan_fisik = isset($request->pemeriksaan_fisik) ? $request->pemeriksaan_fisik : null;
            $rekap_medik->assessment = isset($request->assessment) ? $request->assessment : null;
            $rekap_medik->rencana_dan_terapi = isset($request->rencana_dan_terapi) ? $request->rencana_dan_terapi : null;

            # Jika gagal simpan SOAP ke tabel resep_obat
            if (!$rekap_medik->save()) {
                DB::rollback();
                return Help::resApi('Gagal menyimpan SOAP',400);
            }

            $total = 0;
            if(isset($request->detail_obat)) {
                foreach ($request->detail_obat as $k => $v) {
                    $total += (float)$v['harga'] * (integer)$v['qty'];
                }
                if(!$pengaturan = PengaturanTelemedicine::first()){
                    DB::rollback();
                    return Help::resApi('Terjadi kesalahan sistem, Error pengaturan telemedicine, silahkan hubungi admin',400);
                }

                $payment = new PaymentPermintaan;
                $payment->permintaan_id = $request->id_permintaan_telemedicine;
                $payment->nominal = $total;
                $payment->ongkos_kirim = (float)$pengaturan->biaya_per_km * ceil($permintaan->jarak);
                $payment->jenis_layanan = 'eresep_telemedicine';
                $payment->tgl_expired = date('Y-m-d H:i:s', strtotime('+30 minute'));
                $payment->status = 'UNCONFIRMED';
                if (!$payment->save()) {
                    DB::rollback();
                    return Help::resApi('Gagal saat menyimpan data invoice dari obat yang disimpan',400);
                }

                # Cari jika permintaan telemedis nya sudah/belum diproses resep sebelumnya
                if($resep = ResepObat::where('permintaan_id', $request->id_permintaan_telemedicine)->where('jenis_layanan','telemedicine')->first()) {
                    DB::rollback();
                    return Help::resApi('Tidak bisa merubah resep yang sudah tersimpan sebelumnya',400);
                }
                # Jika belum di proses, maka akan dibuat model resep baru
                $resep = new ResepObat;
                $resep->permintaan_id = $request->id_permintaan_telemedicine;

                # Jika soap dan resep baru, maka digenerate nomor resep
                $resep->no_resep = Help::generateNoResepTelemedicine();
                $resep->status_pembayaran = 'belum';

                # Jika sistemnya bisa update resep, maka perlu di cek apakah resep sebelumnya sudah dilunasi
                if($resep->status_pembayaran != 'belum'){
                    DB::rollback();
                    return Help::resApi('Gagal memperbarui resep, status pembayaran tidak valid',400);
                }

                $resep->total_bayar = $total;
                $resep->tgl_resep = date('Y-m-d H:i:s');
                $resep->berlaku_sampai = date('Y-m-d H:i:s', strtotime('+2 day'));
                $resep->jenis_layanan = 'telemedicine';

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

            # Akhiri layanan vicon jika belum diakhiri
            $sekarang = date('Y-m-d H:i:s');
            if(date('Y-m-d H:i:s', strtotime($vicon->waktu_selesai)) > $sekarang){
                $vicon->waktu_selesai = $sekarang;
                if(!$vicon->save()){
                    DB::rollback();
                    return Help::resApi('Gagal mengakhiri layanan meeting',400);
                }
            }
            
            if(!$activity = Activity::store(Auth::user()->id,'Simpan soap dan eresep telemedicine')) {
                DB::rollback();
                return Help::resApi('Gagal simpan soap dan eresep telemedicine',400);
            }

            DB::commit();
            // DB::rollback();
            return Help::resApi('Resep Berhasil Disimpan',200);

        } catch (\Throwable $e) {
            DB::rollback();
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR SAVE RESEP TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
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

        DB::beginTransaction();

        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try {
            if(!$vicon = VideoConference::where('id_video_conference', $request->id_video_conference)->where('jenis_layanan', 'telemedicine')->with('permintaan_telemedicine')->has('permintaan_telemedicine')->first()){
                return Help::resApi('Tidak ditemukan link pelayanan',204);
            }

            $sekarang = date('Y-m-d H:i:s');
            $jadwal_dokter = date('Y-m-d H:i:s', strtotime($vicon->permintaan_telemedicine->tanggal_kunjungan.$vicon->permintaan_telemedicine->jadwal_dokter));
            if($jadwal_dokter>$sekarang){
                return Help::resApi('Jam layanan belum di mulai', 400);
            }
            if($vicon->permintaan_telemedicine->status_pasien != 'proses') {
                return Help::resApi('Permintaan Telemedicine sedang tidak dalam pelayanan', 400);
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
                    DB::rollback();
                    return Help::resApi('Gagal memulai layanan', 400);
                }
                if(!$activity = Activity::store(Auth::user()->id,'Dokter layanan telemedicine dimulai')) {
                    DB::rollback();
                    return Help::resApi('Gagal memulai telemedicine',400);
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
            $log = ['ERROR GET FORM RESEP OBAT TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getPenilaian(Request $request) {
        $validate = Validator::make($request->all(),[
            'dokter_id' => 'required'
        ],[
            'dokter_id.required' => 'Id Dokter Wajib Di isi'
        ]);

        if ($validate->fails()) {
            return Help::resApi($validate->errors()->all()[0],400);
        }

        try {
            $penilaianTele = Rating::has('permintaan_telemedicine')
                ->whereHas('permintaan_telemedicine',function($q) use($request) {
                    $q->select('id_permintaan_telemedicine','nama')->where('tenaga_medis_id',$request->dokter_id);
                })
                ->with('permintaan_telemedicine',function($q) use($request) {
                    $q->select('id_permintaan_telemedicine','nama')->where('tenaga_medis_id',$request->dokter_id);
                })
                ->where('jenis_layanan','telemedicine');
            // $penilaianHc = Rating::with('permintaan_hc:id_permintaan_hc,nama');
            // $penilaianMcu = Rating::with('permintaan_mcu:id_permintaan,nama');
            // $penilaian = $penilaianTele->union($penilaianHc)->union($penilaianMcu)->get();
            $penilaian = $penilaianTele->get();
            return Help::resApi('Berhasil', 200, $penilaian);
        } catch (\Throwable $e) {
            # Index $log [0{title} , 1{status(true or false)} , 2{errMsg} , 3{errLine} , 4{data}]
            $log = ['ERROR GET PENILAIAN DOKTER TELEMEDICINE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);

            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
