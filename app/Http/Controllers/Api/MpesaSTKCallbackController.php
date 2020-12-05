<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Cartlist;
use App\Http\Requests\Order\MpesaRequest;
use App\Http\Resources\Cartlist\CartlistCollection;
use App\Http\Resources\Order\Order as OrderResource;
use App\Models\Order;
use App\Models\Charge;
use App\Models\Billing;
use App\Models\MpesaTransaction;
use App\Models\MpesaTransError;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;


class MpesaSTKCallbackController extends Controller
{
    public $order;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMpesaCredentials($req)
    {
        //mpesa
        $api_URL = env('MPESA_STK_API');
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $BusinessShortCode = 174379;
        $timestamp = $lipa_time;
        $password = base64_encode($BusinessShortCode . $passkey . $timestamp);
        $callbackurl = 'https://f811480d55d0.ngrok.io/api/payment/validation';
        $AccountReference = 'Priesthood Collections';
        $TransactionDesc = 'Testing Process Activation';

        $api_post_data = [
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 1,
            'PartyA' => $req->mpesaNo, // replace this with your phone number
            'PartyB' => $BusinessShortCode,
            'PhoneNumber' => $req->mpesaNo, // replace this with your phone number
            'CallBackURL' => $callbackurl,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc
        ];

        $errConn = false;
        try {
            $response_data = Http::withToken($this->generateAccessToken())->post($api_URL, $api_post_data);
            $results = $response_data->json();
            $r = response()->json($results)->original;
            // $r->original['errorMessage']
            if (isset($r['ResponseCode'])) {
                if ($this->url_exists($callbackurl)) {
                    $res = [
                        'ResponseCode' => 0,
                        'ResponseErr' => false,
                        'errConn' => $errConn
                    ];

                    $order = Order::find($req->orderId);
                    $this->mpesaValidation($order);
                    return  $res;
                }
            } else {
                $res = [
                    'ResponseCode' => 1,
                    'ResponseErr' => $r,
                    'errConn' => $errConn
                ];
                return  $res;
            }
        } catch (\Exception $e) {
            $errConn = true;
            return ['errConn' => $errConn];
        }
    }

    public function url_exists($url)
    {
        return curl_init($url) !== false;
    }

    public function generateAccessToken()
    {
        $consumer_key = env('MPESA_APP_CONSUMER_KEY');
        $consumer_secret = env('MPESA_APP_CONSUMER_SECRET');
        $credentials = "Basic " . base64_encode($consumer_key . ":" . $consumer_secret);
        $api_URL = env('MPESA_AUTH_API');
        //get access token
        $response_access = Http::withHeaders(['Basic' => '', 'Authorization' => $credentials])->get($api_URL);
        if (!$response_access->successful()) {
            return;
        }

        $token_data = $response_access->json();
        return $token_data['access_token'];
    }

    /**
     *  M-pesa Validation Method
     * Safaricom will only call your validation if you have requested by writing an official letter to them
     */


    public function mpesaValidation($order = null)
    {
        $callback_data = request()->all();
        //save mpesa transaction data
        // $mpesaTrans = new MpesaTransaction();
        // $mpesaTrans->userId = request()->user()->id;

        // $call_metadata = false;

        // foreach ($callback_data as $f) {
        //     $mpesaTrans->ResultCode = $f['stkCallback']['ResultCode'];
        //     $mpesaTrans->ResultDesc = $f['stkCallback']['ResultDesc'];
        //     //
        //     if (isset($f['stkCallback']['CallbackMetadata'])) {
        //         $call_metadata = true;
        //         foreach ($f['stkCallback']['CallbackMetadata']['Item'] as $k) {
        //             $name = $k['Name'];
        //             if ($name === 'Amount') {
        //                 $mpesaTrans->Amount = $k['Value'];
        //             } else if ($name === 'MpesaReceiptNumber') {
        //                 $mpesaTrans->MpesaReceiptNumber = $k['Value'];
        //             } else if ($name === 'Balance') {
        //                 isset($k['Value']) ? $mpesaTrans->Balance = $k['Value'] :  "0.00";
        //             } else if ($name === 'TransactionDate') {
        //                 $mpesaTrans->TransactionDate = $k['Value'];
        //             } else if ($name === 'PhoneNumber') {
        //                 $mpesaTrans->PhoneNumber = $k['Value'];
        //             }
        //         }
        //     }
        // }

        //erro

        if (isset($callback_data['Body']['stkCallback'])) {
            $err = $callback_data['Body']['stkCallback']['ResultDesc'];
            if ($order) {
                // $bill = Billing::find($order->billing_id);
                // $bill->error_desc = $err;
                // Log::channel('customlog')->info($bill);
            }
        }





        // Log::channel('customlog')->info($callback_data);
        // Log::channel('customlog')->info($order);

        return response()->json(['result' => $callback_data]);


        // if ($result_code > 0) {
        //     # code...
        // } else {
        // }

        // $result_code = "0";
        // $result_description = "Accepted validation request.";
        // return $this->createValidationResponse($result_code, $result_description);
    }
}
