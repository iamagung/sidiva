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
use App\Models\PermintaanTelemedicine;
use DB;

class LaporanLayananTelemedicineExport implements FromView, WithStyles, WithDefaultStyles
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
                'layananTelemedicine' => PermintaanTelemedicine::select(
                    'id_permintaan_telemedicine',
                    'tanggal_order',
                    'no_rm',
                    'nama',
                    'permintaan_telemedicine.no_telepon',
                    'poli_id',
                    'tenaga_medis_id',
                    'tanggal_kunjungan',
                    'jadwal_dokter',
                    'status_pembayaran',
                    'status_pasien',
                    'perawat_id'
                )
                ->with('tmPoli:KodePoli,NamaPoli')
                ->with('nakes:id,name')
                ->with('nakes_perawat:id,name')
                ->whereMonth('tanggal_kunjungan', $this->bulan)
                ->whereYear('tanggal_kunjungan', $this->tahun)
                ->orderBy('id_permintaan_telemedicine','DESC')->get()
                ]);
        } else {
            return view('admin.laporan.excel-template', [
                'layananTelemedicine' => PermintaanTelemedicine::select(
                    'id_permintaan_telemedicine',
                    'tanggal_order',
                    'no_rm',
                    'nama',
                    'permintaan_telemedicine.no_telepon',
                    'poli_id',
                    'tenaga_medis_id',
                    'tanggal_kunjungan',
                    'jadwal_dokter',
                    'status_pembayaran',
                    'status_pasien',
                    'perawat_id'
                )
                ->with('tmPoli:KodePoli,NamaPoli')
                ->with('nakes:id,name')
                ->with('nakes_perawat:id,name')
                ->orderBy('id_permintaan_telemedicine','DESC')->get()
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
