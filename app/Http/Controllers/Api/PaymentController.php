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
use App\Http\Controllers\Api\MpesaSTKCallbackController;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaSTKCallbackController $mpesaService)
    {
        $this->mpesaService =  $mpesaService;
    }

    public function customerPay(Request $request)
    {
        $results = null;
        $bill = Billing::where("idf_no", $request->idfNo)->first();
        if (!$bill) {
            return;
        }

        $cartlist = Cartlist::whereIn('id', $request->cartIds)->get();
        $productsSKU = [];
        foreach ($cartlist as $cart) {
            $cart->processed = 1;
            $cart->update();
            array_push($productsSKU, $cart->SKU);
        }
        //find order
        $order = Order::find($request->orderId);
        if (!$order) {
            return;
        }
        // update mpesa no
        $addressMob =  "+" . $request->mpesaNo;
        $address =  $order->address;
        if ($address->mobile_no !== $addressMob) {
            $address->mobile_no = $addressMob;
            $address->update();
        }
        // create charge
        // $randNo = Str::random();
        // $charges = Charge::create(array(
        //     "amount" => $order->total,
        //     "currency" => "KES",
        //     "source" => $randNo,
        //     "description" => "Charge order for " . $order->order_no,
        //     'user_id' => $request->user()->id
        // ));

        // $charges = 1;
        //update order
        $order->cart = json_encode($cartlist);
        // $order->payment_id = $charges->id;
        $order->order_status_id = 2;
        $order->cart_thumb = $cartlist[0]->ProductThumb;
        $order->update();
        //check stock
        $stocks = Stock::whereIn('sku', $productsSKU)->get();
        if (count($stocks) > 0) {
            foreach ($stocks as $stock) {
                $stock->stock_quantity =   $stock->stock_quantity !== 0 ?  (int) $stock->stock_quantity - 1 : 0;
                $stock->update();
            }
        }
        // clear cart
        // Cartlist::whereIn('id', $request->cartIds)->delete();

        $mpesaRes = $this->mpesaService->getMpesaCredentials($request);
        if (!$mpesaRes['errConn']) {
            // if (!$mpesaRes['errConn']) {
            //     return response()->json($mpesaRes);
            // } else {
            //     return response()->json('dd');
            // }
            return response()->json($mpesaRes);
        } else {
            //update error
            $order->order_status_id = 2;
            $order->update();
            $bill->error_desc = 'Payment unable to be proccessed';
            $bill->update();

            $results = ['ResponseCode' => 1, 'error' => $bill->error_desc];
        }


        return response()->json(['order' => new OrderResource($order), 'results' => $results]);
    }
}
