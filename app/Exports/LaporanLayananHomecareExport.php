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
use App\Models\PermintaanHC;
use DB;

class LaporanLayananHomecareExport implements FromView, WithStyles, WithDefaultStyles
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan,$tahun) {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        if (!empty($this->bulan)) {
            return view('admin.laporan.excel-template', [
                'layananHomecare' => PermintaanHC::whereMonth('tanggal_kunjungan', $this->bulan)
                ->whereYear('tanggal_kunjungan', $this->tahun)
                ->with('layanan_permintaan_hc',function($qq) {
                    $qq->with('layanan_hc');
                })
                ->orderBy('id_permintaan_hc','DESC')->get()
            ]);
        } else {
            return view('admin.laporan.excel-template', [
                'layananHomecare' => PermintaanHC::orderBy('id_permintaan_hc','DESC')
                ->with('layanan_permintaan_hc',function($qq) {
                    $qq->with('layanan_hc');
                })
                ->get()
            ]);
        }
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
