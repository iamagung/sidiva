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

class LaporanKeuanganHomecare implements FromView, WithStyles, WithDefaultStyles
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
            'paymentHomecare' =>  PaymentPermintaan::select(
            'payment_permintaan.permintaan_id',
            'payment_permintaan.nominal',
            'payment_permintaan.jenis_layanan',
            'payment_permintaan.status',
            'payment_permintaan.ongkos_kirim',
            'pc.id_permintaan_hc',
            'pc.no_rm',
            'pc.nama',
            'pc.alamat',
            'pc.tanggal_order',
            'pc.tanggal_kunjungan'
            )
            ->with('permintaan_hc',function($q) {
                $q->with('layanan_permintaan_hc',function($qq) {
                    $qq->with('layanan_hc');
                });
            })
            ->leftJoin('permintaan_hc as pc','pc.id_permintaan_hc','payment_permintaan.permintaan_id')
            ->whereBetween('pc.tanggal_kunjungan', [$this->awal, $this->akhir])
            ->where('payment_permintaan.jenis_layanan','homecare')
            ->orderBy('pc.id_permintaan_hc','DESC')->get()
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
