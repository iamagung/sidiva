<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\PermintaanTelemedicine;
use App\Models\PaymentPermintaan;

class RiwayatPermintaanTelemedicineExport implements FromCollection
{
    protected $min;
    protected $max;
    protected $status;

    function __construct($min,$max,$status) {
        $this->min = $min;
        $this->max = $max;
        $this->status = $status;
    }

    public function collection()
    {
        return PaymentPermintaan::all();
        // return PermintaanTelemedicine::all();
        // return PermintaanTelemedicine::select('poli_id', 'permintaan_telemedicine.tanggal_order', 'permintaan_telemedicine.no_rm', 'permintaan_telemedicine.nama', 'tenaga_medis_id', 'perawat_id', 'tanggal_kunjungan', 'status_pasien')
        //             ->with('tmPoli:NamaPoli,KodePoli')
        //             ->with(['dokter' => function($q) {
        //                 $q->select('nakes_id')->with('user_ranap:id,name as nama_dokter');
        //             }])
        //             ->with(['perawat' => function($q) {
        //                 $q->select('nakes_id')->with('user_ranap:id,name as nama_perawat');
        //             }])
        //             ->when($this->status!='all',fn($q) =>
        //                 $q->where('status_pasien', $this->status)
        //             )
        //             ->whereBetween('tanggal_order', [$this->min, $this->max])
        //             ->orderBy('permintaan_telemedicine.created_at','ASC')->get();
    }
}
