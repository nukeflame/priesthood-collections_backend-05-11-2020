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
use Auth;
use App\Http\Controllers\Api\MpesaSTKCallbackController;

class PaymentController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaSTKCallbackController $mpesaService)
    {
        $this->mpesaService =  $mpesaService;
    }

    public function customerMpesaSTKPush(MpesaRequest $request)
    {
        $cartlist = Cartlist::whereIn('id', $request->cartIds)->get();
        $productsSKU = [];
        foreach ($cartlist as $cart) {
            $cart->processed = 1;
            $cart->update();
            array_push($productsSKU, $cart->SKU);
        }
        //find order
        $order = Order::findOrFail($request->orderId);
        // update mpesa no
        $addressMob =  "+" . $request->mpesaNo;
        $address =  $order->address;
        if ($address->mobile_no !== $addressMob) {
            $address->mobile_no = $addressMob;
            $address->update();
        }
        // create charge
        $charges = Charge::create(array(
            "amount" => $order->total,
            "currency" => "kes",
            "source" => '038458ML',
            "description" => "Mpesa Transaction",
            'transaction_id' => 1
        ));
        //update order
        $order->cart = json_encode($cartlist);
        $order->payment_id = $charges->id;
        $order->order_status_id = 3;
        $order->cart_thumb = $cartlist[0]->ProductThumb;
        $order->update();
        //check stock
        $stocks = Stock::whereIn('sku', $productsSKU)->get();
        foreach ($stocks as $stock) {
            $stock->stock_quantity =   $stock->stock_quantity !== 0 ?  (int) $stock->stock_quantity - 1 : 0;
            $stock->update();
        }
        // clear cart
        // Cartlist::whereIn('id', $request->cartIds)->delete();

        //mpesa auth
        // $mpesaData = [
        //     'mpesaNo' => $addressMob,
        //     'mpesaId' => $addressMob,
        // ];

        // $mpesa = $this->mpesaService->getMpesaCredentials($mpesaData);

        return response()->json($request->all());

        // $api_URL = env('MPESA_STK_API');
        // $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        // $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        // $BusinessShortCode = 174379;
        // $timestamp = $lipa_time;
        // $password = base64_encode($BusinessShortCode.$passkey.$timestamp);

        // $AccountReference = 'Priesthood Collections';
        // $TransactionDesc = 'Testing Process Activation';

        // $api_post_data = [
        //     'BusinessShortCode' => $BusinessShortCode,
        //     'Password' => $password,
        //     'Timestamp' => $timestamp,
        //     'TransactionType' => 'CustomerPayBillOnline',
        //     'Amount' => 1,
        //     'PartyA' => 254768865997, // replace this with your phone number
        //     'PartyB' => $BusinessShortCode,
        //     'PhoneNumber' => 254768865997, // replace this with your phone number
        //     'CallBackURL' => 'https://7a447e2f7e2b.ngrok.io/api/payment/validation',
        //     'AccountReference' => $AccountReference,
        //     'TransactionDesc' => $TransactionDesc
        // ];

        // $response_data = Http::withToken($this->generateAccessToken())->post($api_URL, $api_post_data);
        // $results = $response_data->json();

        // $results = [
        //     'ResponseCode' => 1
        // ];

        //save
        // $this->mpesaValidation();

        // return response()->json(['order' => new OrderResource($order), 'results' => $results ]);
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

    public function mpesaValidation()
    {
        // Request
        // \Log::info($request->all());
        $result_code  = 1;

        $data = [
            'ResultDesc' => 'The balance is insufficient for the transaction',
            'PhoneNumber' => 0700412127,
            'TransAmount' => 12127,
            'Balance' => 0700412127,
            'MpesaReceiptNumber' => 'OFI11SPW9V',
            'FirstName' => 'Ken',
            'MiddleName' => 'Peters',
            'LastName' => 'Ken',
            'TransactionDate' => Carbon::now()
        ];

        $mpesa = new MpesaTransaction();
        if ($result_code > 0) {
            $mpesa->PhoneNumber = $data['PhoneNumber'];
            $mpesa->TransAmount =  0;
            $mpesa->OrgAccountBalance = 0;
            $mpesa->MpesaReceiptNumber = '---';
            $mpesa->FirstName = $data['FirstName'];
            $mpesa->MiddleName = $data['MiddleName'];
            $mpesa->LastName = $data['LastName'];
            $mpesa->TransactionDate = Carbon::now();
            $mpesa->ResultDesc = $data['ResultDesc'];
            $mpesa->save();
        } else {
            $mpesa->PhoneNumber = $data['PhoneNumber'];
            $mpesa->TransAmount = $data['TransAmount'];
            $mpesa->OrgAccountBalance = $data['Balance'];
            $mpesa->MpesaReceiptNumber = $data['MpesaReceiptNumber'];
            $mpesa->FirstName = $data['FirstName'];
            $mpesa->MiddleName = $data['MiddleName'];
            $mpesa->LastName = $data['LastName'];
            $mpesa->TransactionDate = Carbon::now();
            $mpesa->ResultDesc = $data['ResultDesc'];
            $mpesa->save();
        }







        //     'Body' =>
        //     array (
        //       'stkCallback' =>
        //       array (
        //         'MerchantRequestID' => '20024-749397-1',
        //         'CheckoutRequestID' => 'ws_CO_180620200809491565',
        //         'ResultCode' => 1,
        //         'ResultDesc' => 'The balance is insufficient for the transaction',
        //       ),
        //     ),
        //   )

        // [2020-06-18 08:31:11] local.INFO: array (
        //     'Body' =>
        //     array (
        //       'stkCallback' =>
        //       array (
        //         'MerchantRequestID' => '1171-3029538-1',
        //         'CheckoutRequestID' => 'ws_CO_180620200829580144',
        //         'ResultCode' => 0,
        //         'ResultDesc' => 'The service request is processed successfully.',
        //         'CallbackMetadata' =>
        //         array (
        //           'Item' =>
        //           array (
        //             0 =>
        //             array (
        //               'Name' => 'Amount',
        //               'Value' => 1.0,
        //             ),
        //             1 =>
        //             array (
        //               'Name' => 'MpesaReceiptNumber',
        //               'Value' => 'OFI11SPW9V',
        //             ),
        //             2 =>
        //             array (
        //               'Name' => 'Balance',
        //             ),
        //             3 =>
        //             array (
        //               'Name' => 'TransactionDate',
        //               'Value' => 20200618083057,
        //             ),
        //             4 =>
        //             array (
        //               'Name' => 'PhoneNumber',
        //               'Value' => 254768865997,
        //             ),
        //           ),
        //         ),
        //       ),
        //     ),
        //   )



        // if ($result_code > 0) {
        //     # code...
        // } else {
        // }

        // $result_code = "0";
        // $result_description = "Accepted validation request.";
        // return $this->createValidationResponse($result_code, $result_description);
    }
}
