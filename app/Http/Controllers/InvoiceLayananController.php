<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PermintaanHC;
use App\Models\LayananHC;
use App\Models\LayananPermintaanHc;
use App\Models\TenagaMedisHomecare;
use App\Models\TenagaMedisPermintaanHomecare;
use App\Helpers\Helpers as Help;
use Validator, DB, Auth, PDF;

class InvoiceLayananController extends Controller
{
    public function view($id, $jenis) {
        try{
            if ($jenis=='homecare') {
                $data = Help::dataRegistrasiHomecare($id);
                return view('admin.invoice.homecare', $data);
            } else if ($jenis=='telemedicine') {
                $data = Help::dataRegistrasiTelemedicine($id);
                $data['isView'] = true;
                return view('admin.invoice.telemedicine', $data);
            } else if ($jenis=='eresep_telemedicine') {
                $data = Help::dataEresepTelemedicine($id);
                $data['isView'] = true;
                return view('admin.invoice.eresep_telemedicine', $data);
            } else {
                $data = Help::dataRegistrasiMCU($id);
                return view('admin.invoice.mcu', $data);
            }
        } catch (\Throwable $e) {
            $log = ['ERROR GET INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
    public function download($id, $jenis) {
        try{
            if ($jenis=='homecare') {
                $data = Help::dataRegistrasiHomecare($id);
                return view('admin.invoice.homecare', $data);
            } else if ($jenis=='telemedicine') {
                $data = Help::dataRegistrasiTelemedicine($id);
                $html = view('admin.invoice.telemedicine', $data)->render();
                $pdf = PDF::loadHTML($html, 'utf-8');
                $pdf->setOptions([
                    // 'dpi' => 50,
                    'isHtml5ParserEnabled' => true,
                    'defaultFont' => 'sans-serif',
                    'isPhpEnabled' => true,
                    'isJavascriptEnabled' => true
                ]);
                $pdf->set_paper('a5', 'portrait');
                // return $pdf->download('invoice_telemedicine.pdf');
                return $pdf->stream();
            } else if ($jenis=='eresep_telemedicine') {
                $data = Help::dataEresepTelemedicine($id);
                $html = view('admin.invoice.eresep_telemedicine', $data)->render();
                $pdf = PDF::loadHTML($html, 'utf-8');
                $pdf->setOptions([
                    // 'dpi' => 50,
                    'isHtml5ParserEnabled' => true,
                    'defaultFont' => 'sans-serif',
                    'isPhpEnabled' => true,
                    'isJavascriptEnabled' => true
                ]);
                $pdf->set_paper('a5', 'portrait');
                // return $pdf->download('invoice_telemedicine.pdf');
                return $pdf->stream();
            } else {
                $data = Help::dataRegistrasiMCU($id);
                return view('admin.invoice.mcu', $data);
            }
        } catch (\Throwable $e) {
            $log = ['ERROR GET INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
