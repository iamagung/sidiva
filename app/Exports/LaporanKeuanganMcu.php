<?php

namespace App\Exports;

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

class LaporanKeuanganMcu implements FromView, WithStyles, WithDefaultStyles
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
    public function view(): View
    {
        return view('admin.laporan.excel-template', [
            'paymentMcu' => PaymentPermintaan::select(
            'payment_permintaan.permintaan_id',
            'payment_permintaan.nominal',
            'payment_permintaan.jenis_layanan',
            'payment_permintaan.status',
            'payment_permintaan.ongkos_kirim',
            'pu.id_permintaan',
            'pu.no_rm',
            'pu.nama',
            'pu.alamat',
            'pu.tanggal_order',
            'pu.tanggal_kunjungan',
            'pu.jenis_mcu'
            )
            ->with('permintaan_mcu',function ($qq) {
                $qq->with('layanan_permintaan_mcu', function($q) {
                    $q->with('layanan_mcu');
                });
            })
            ->leftJoin('permintaan_mcu as pu','pu.id_permintaan','payment_permintaan.permintaan_id')
            ->whereBetween('pu.tanggal_kunjungan', [$this->awal, $this->akhir])
            ->where('payment_permintaan.jenis_layanan','mcu')
            ->orderBy('pu.id_permintaan','DESC')->get()
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
