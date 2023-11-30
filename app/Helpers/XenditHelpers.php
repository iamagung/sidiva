<?php
namespace App\Helpers;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Refund\RefundApi;
use Config;
use App\Helpers\Helpers as Help;

class XenditHelpers{

    public static function setKey() {
        // return config('xendit.api_key');
        Configuration::setXenditKey(config('xendit.api_key'));
    }

    public function createInvoice($external_id, $description, $amount, $invoice_duration, $items) {
        Self::setKey();
        $apiInstance = new InvoiceApi();
        $create_invoice_request = (object) [
            "external_id"=>$external_id,
            "description"=>$description,
            "amount"=>$amount,
            "invoice_duration"=>$invoice_duration,
            "currency"=>"IDR",
            "reminder_time"=>1,
            "items"=>$items
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

    public function expiredInvoice($invoice_id) {
        Self::setKey();
        $apiInstance = new InvoiceApi();

        try {
            $result = $apiInstance->expireInvoice($invoice_id);
            if ($result) {
                return Help::resApi('Ok',200,$result);
            }
            return Help::resApi('Gagal terhubung ke Xendit',500);
        } catch (\Xendit\XenditSdkException $e) {
            $log = ['XENDIT ERROR REQUEST EXPIRED INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getFullError()];
            Help::logging($log);
            return Help::resApi('eror xendit',$e->getCode()>=200?$e->getCode():500);
        } catch (\Exception $e) {
            $log = ['SISTEM ERROR REQUEST EXPIRED INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('eror sistem',500);
        }
    }

    public function createRefund($invoice_id = '655486df2b2f4e043a28392e') {
        Self::setKey();
        $apiInstance = new RefundApi();

        $idempotency_key = "9797b5a6-54ad-4511-80a4-ec451346808b"; // string
        $for_user_id = "5f9a3fbd571a1c4068aa40ce"; // string
        // $create_refund = new \Xendit\Refund\CreateRefund();
        $create_refund = (object) [
            "invoice_id"=>$invoice_id
        ];

        try {
            $result = $apiInstance->createRefund(null, null, $create_refund);
            if ($result) {
                return Help::resApi('Ok',200,$result);
            }
            return Help::resApi('Gagal terhubung ke Xendit',500);
        } catch (\Xendit\XenditSdkException $e) {
            $log = ['XENDIT ERROR REQUEST EXPIRED INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getFullError()];
            Help::logging($log);
            return Help::resApi('eror xendit',$e->getCode()>=200?$e->getCode():500);
        } catch (\Exception $e) {
            $log = ['SISTEM ERROR REQUEST EXPIRED INVOICE ('.$e->getFile().')',false,$e->getMessage(),$e->getLine()];
            Help::logging($log);
            return Help::resApi('eror sistem',500);
        }
    }
}
