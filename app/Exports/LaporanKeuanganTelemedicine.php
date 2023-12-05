<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Contracts\View\View;
use App\Models\PaymentPermintaan;
use DB;

class LaporanKeuanganTelemedicine implements FromView, WithStyles, WithDefaultStyles
{
    protected $awal;
    protected $akhir;

    public function __construct($awal,$akhir) {
        $this->awal = $awal;
        $this->akhir = $akhir;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    public function view(): View
    {
        return view('admin.laporan.excel-template', [
            'paymentTelemedicine' => PaymentPermintaan::select(
                'payment_permintaan.permintaan_id',
                'payment_permintaan.nominal',
                'payment_permintaan.jenis_layanan',
                'payment_permintaan.status',
                'payment_permintaan.ongkos_kirim',
                'pt.id_permintaan_telemedicine',
                'pt.no_rm',
                'pt.nama',
                'pt.alamat',
                'pt.tanggal_order',
                'pt.tanggal_kunjungan',
                'pt.tenaga_medis_id',
                'pt.perawat_id',
                'pt.poli_id',
                'ro.diantar'
            )
            ->with('permintaan_telemedicine',function($q) {
                $q->select('poli_id','id_permintaan_telemedicine','tenaga_medis_id','perawat_id')
                ->with('tmPoli')
                ->with('dokter',function($qq) {
                    $qq->with('user_ranap');
                })
                ->with('perawat',function($qq) {
                    $qq->with('user_ranap');
                });
            })
            ->leftJoin('permintaan_telemedicine as pt','pt.id_permintaan_telemedicine','payment_permintaan.permintaan_id')
            ->leftJoin('resep_obat as ro','ro.permintaan_id','payment_permintaan.permintaan_id')
            ->whereBetween('pt.tanggal_kunjungan', [$this->awal, $this->akhir])
            ->whereIn('payment_permintaan.jenis_layanan',['telemedicine','eresep_telemedicine'])
            ->where('ro.jenis_layanan','telemedicine')
            ->orderBy('pt.id_permintaan_telemedicine','DESC')->get()
        ]);

    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'b3fcff'],
                ],
            ],
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        return [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ],
        ];
    }
}
