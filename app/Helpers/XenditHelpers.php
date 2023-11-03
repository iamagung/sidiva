<?php
namespace App\Helpers;

use Xendit\Configuration;
// use Xendit\BalanceAndTransaction\BalanceApi;
use Xendit\PaymentRequest\PaymentRequestApi;
use Config;

class XenditHelpers{

    public static function setKey() {
        // return config('xendit.api_key');
        Configuration::setXenditKey(config('xendit.api_key'));
    }

    public function buatCharge() {
        self::setKey();
        $apiInstance = new PaymentRequestApi();
        // $idempotency_key = "5f9a3fbd571a1c4068aa40ce"; // string
        // $for_user_id = "5f9a3fbd571a1c4068aa40ce"; // string
        $payment_request_parameters = (object) [
            "reference_id"=>"example-ref-1234",
            "amount"=>15000,
            "currency"=>"IDR",
            "country"=>"ID",
            "payment_method"=> (object) [
                "type"=>"EWALLET",
                "ewallet"=>(object)[
                    "channel_code"=>"OVO",
                    "channel_properties"=>[
                        "mobile_number"=> "+6289665116467"
                    ]
                ],
                "reusability"=>"ONE_TIME_USE"
            ]
        ]; // \Xendit\PaymentRequest\PaymentRequestParameters

        try {
            $result = $apiInstance->createPaymentRequest(null, null, $payment_request_parameters);
            print_r($result);
        } catch (\Xendit\XenditSdkException $e) {
            echo 'Exception when calling PaymentRequestApi->createPaymentRequest: ', $e->getMessage(), PHP_EOL;
            echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
        }
    }


}
