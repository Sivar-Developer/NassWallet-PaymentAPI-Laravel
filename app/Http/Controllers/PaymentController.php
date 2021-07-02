<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Redirect;


class PaymentController extends Controller
{

    private  $baseURl = 'https://uatgw1.nasswallet.com/payment/transaction';

    // Merchant Account
    Private $username = "";   // username
    Private $password = "";    // password
    Private $transactionPin = ""; //  MPIN
    Private $grantType = "password";

    Private $orderId = "1234";
    Private $amount = "10";
    Private $languageCode = "en";
    Private $basicToken = "Basic TUVSQ0hBTlRfUEFZTUVOVF9HQVRFV0FZOk1lcmNoYW50R2F0ZXdheUBBZG1pbiMxMjM=";


    // POST: /payment
    public function makePayment() {
        $response = $this->getMerchantToken();
        $payload = [
            'data' => [
            'userIdentifier' => $this->username,
            'transactionPin' => $this->transactionPin,
            'orderId' => $this->orderId,
            'amount' => $this->amount,
            'languageCode' => $this->languageCode
            ]
        ];

        if($response->responseCode == 0 && $response->data->access_token) {
            return $this->payWithNasswallet($response->data->access_token, $payload);
        } else {
            return dd("$response->message , $response->errCode");
        }
    }

    private function getMerchantToken() {
        $payload = [
            'data' => [
                'username' => $this->username,
                'password' => $this->password,
                'grantType' => $this->grantType
            ]
        ];

       return \GuzzleHttp\json_decode(Http::withHeaders([
            'authorization' => "{$this->basicToken}"
        ])->post("{$this->baseURl}/login", $payload)->body());
    }

    private function payWithNasswallet($access_token, $payload) {

        $response = \GuzzleHttp\json_decode(Http::withHeaders([
            'authorization' => "Bearer  {$access_token}"
        ])->post("{$this->baseURl}/initTransaction", $payload)->body());

        if($response->responseCode == 0 && $response->data->transactionId) {
            return \redirect()->to("https://uatcheckout1.nasswallet.com/payment-gateway?id={$response->data->transactionId}&token={$response->data->token}&userIdentifier={$payload['data']['userIdentifier']}");
        } else {
            dd('Oops, something went wrong!',"Error Code: {$response->errCode}" );
        }
    }
}
