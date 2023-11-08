<?php
namespace App\Helpers;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Config;
use App\Helpers\Helpers as Help;

class XenditHelpers{

    public static function setKey() {
        // return config('xendit.api_key');
        Configuration::setXenditKey(config('xendit.api_key'));
    }

    public function createInvoice($external_id, $description, $amount, $invoice_duration) {
        Self::setKey();
        $apiInstance = new InvoiceApi();
        $create_invoice_request = (object) [
            "external_id"=>$external_id,
            "description"=>$description,
            "amount"=>$amount,
            "invoice_duration"=>$invoice_duration,
            "currency"=>"IDR",
            "reminder_time"=>1
        ]; // \Xendit\Invoice\CreateInvoiceRequest

        try {
            $result = $apiInstance->createInvoice($create_invoice_request);
            if ($result) {
                return Help::resApi('Ok',200,$result);
            }
            return Help::resApi('Gagal terhubung ke Xendit',500);
        } catch (\Xendit\XenditSdkException $e) {
            $log = ['XENDIT ERROR REQUEST CREATE INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getFullError()];
            Help::logging($log);
            return Help::resApi('error xendit',$e->getCode());
        } catch (\Exception $e) {
            $log = ['SISTEM ERROR REQUEST CREATE INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getFullError()];
            Help::logging($log);
            return Help::resApi('error sistem',500);
        }
    }

    public function getInvoice($invoice_id) {
        Self::setKey();
        $apiInstance = new InvoiceApi();

        try {
            $result = $apiInstance->getInvoiceById($invoice_id);
            if ($result) {
                return Help::resApi('Ok',200,$result);
            }
            return Help::resApi('Gagal terhubung ke Xendit',500);
        } catch (\Xendit\XenditSdkException $e) {
            $log = ['XENDIT ERROR REQUEST GET INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getFullError()];
            Help::logging($log);
            return Help::resApi('eror xendit',$e->getCode()>=200?$e->getCode():500);
        } catch (\Exception $e) {
            $log = ['SISTEM ERROR REQUEST GET INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('eror sistem',500);
        }
    }
}
