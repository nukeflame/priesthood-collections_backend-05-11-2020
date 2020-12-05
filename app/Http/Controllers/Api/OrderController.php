<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Order\OrderRequest;
use App\Models\Order;
use App\Models\Billing;
use Illuminate\Support\Facades\Auth;
use App\Models\Shipping;
use App\Http\Resources\Order\Order as OrderResource;
use App\Http\Resources\Order\OrderCollection;
use App\Models\Cartlist;
use App\Http\Requests\Order\MpesaRequest;
use App\Http\Resources\Cartlist\CartlistCollection;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        return new OrderCollection($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        $order = new Order();
        if ($request->addressDetails) {
            $order->order_no = mt_rand(100000000, 999999999);
            $order->total = 0;
            $order->order_status_id = 1;
            $order->customer_id = Auth::id();
            $order->shipping_id = 0;
            $order->billing_id = 0;
            $order->save();
        }
        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id) {
            $order = Order::where('order_no', $id)->first();
            if (empty($order)) {
                return;
            }
            return new OrderResource($order);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showOrder(Request $request)
    {
        $auth = Auth::user();
        if ($auth->email === $request->userEmail) {
            $orders = Order::where('customer_id', $auth->id)->orderBy('created_at', 'desc')->get();
            return new OrderCollection($orders);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function authOrder()
    {
        $auth = Auth::user();
        if ($auth) {
            $order = Order::where('customer_id', $auth->id)->latest()->first();
            $cartlist = Cartlist::where(['userId' => $auth->id, 'processed' => 1])->latest()->get();
            return  response()->json(['order' =>  new OrderResource($order), 'cartlist' => new CartlistCollection($cartlist)]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->updateOrder) {
            //update order
            return response()->json($request->all());
        } else {
            $shipping = new Shipping();
            // $shipping->shipping_method = $request->shippingMethod;
            // $shipping->region = $request->pickRegion;
            // $shipping->city = $request->pickCity;
            // $shipping->subtotal_amount = $request->subtotalAmount;
            // $shipping->shipping_amount = $request->shippingMethod;
            // $shipping->total_amount = $request->totalAmount;
            // $shipping->save();

            // //create new order
            // $order = new Order();
            // $order->order_no = str_pad(count(Order::all()) + 1, 6, "0", STR_PAD_LEFT);
            // $order->total = 0;
            // $order->status = 2;
            // $order->customer_id = Auth::id();
            // $order->shipping_id = 0;
            // $order->billing_id = 0;
            // $order->delivery_address = $request->deliveryAddress;
            // $order->firstname = ucfirst($request->firstname);
            // $order->lastname = ucfirst($request->lastname);
            // $order->mobile_no = $request->mobileNo;
            // $order->other_mobile_no	 = $request->otherMobileNo ? $request->otherMobileNo : null;
            // $order->state_region_id = $request->stateRegion;
            // $order->city_id = $request->city;
            // $order->save();
            return response()->json($shipping);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$id) {
            return;
        }
        $orderIds = explode(',', $id);
        $orders = Order::findOrFail($orderIds);
        foreach ($orders as $d) {
            $d->delete();
            Shipping::where('id', $d->shipping_id)->delete();
            Billing::where('id', $d->billing_id)->delete();
        }

        return new OrderCollection($orders);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function makePayment(MpesaRequest $request)
    {
        //order status
        $order = Order::findOrFail($request->orderId);
        $order->order_status_id = 3;
        $order->update();
        $bill = Billing::find($order->billing_id);
        $bill->mobile_no  = $request->mpesaNo;
        $bill->update();

        //clear cartlist
        $cartlist = Cartlist::whereIn('id', $request->cartIds)->get();
        foreach ($cartlist as $cart) {
            $cart->processed = 1;
            $cart->update();
            $order->items()->attach($cart->id);
        }

        //proccesss mpesa transaction

        return response()->json(['order' => new OrderResource($order)]);
    }
}
