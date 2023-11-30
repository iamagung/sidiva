<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helpers as Help;
use App\Models\Artikel;

class ApiArtikelController extends Controller
{
    public function getLimit() {
        try {
            $data = Artikel::limit(4)->get();
            if (count($data) > 0) {
                return Help::custom_response(200, "success", "Ok", $data);
            }
            return Help::custom_response(204, "error", "data tidak ditemukan", null);
        } catch (\Throwable $e) {
            $log = ['ERROR GET LIMIT ARTIKEL ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getAll() {
        try {
            $data = Artikel::all();
            if (count($data) > 0) {
                return Help::custom_response(200, "success", "Ok", $data);
            }
            return Help::custom_response(204, "error", "data tidak ditemukan", null);
        } catch (\Throwable $e) {
            $log = ['ERROR GET ALL ARTIKEL ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }

    public function getDetail($id) {
        try {
            if(!$id){# Pengecekan id nya ada atau tidak
                return Help::custom_response(400, "error", "ID artikel wajib diisi", null);
            }
            $data = Artikel::where('id_artikel_kesehatan',$id)->first();
            if ($data) {
                return Help::custom_response(200, "success", "Ok", $data);
            }
            return Help::custom_response(204, "error", "data tidak ditemukan", null);
        } catch (\Throwable $e) {
            $log = ['ERROR GET DETAIL ARTIKEL ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('Terjadi kesalahan sistem',500);
        }
    }
}
