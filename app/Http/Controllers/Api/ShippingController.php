<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Shipping\ShippingCollection;
use App\Http\Resources\Shipping\Shipping as ShippingResource;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\Billing;
use App\Http\Resources\Order\Order as OrderResource;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth = Auth::user();
        if ($auth) {
            $cart = Shipping::where('user_id', $auth->id)->orderBy('created_at', 'desc')->first();
            if (!empty($cart)) {
                return new ShippingResource($cart);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $order = Order::where('order_no', $id)->first();
        return new OrderResource($order);
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
        $authId = Auth::id();
        $shipping = Shipping::findOrFail($id);
        $shipping->shipping_method = $request->shippingMethod;
        $shipping->subtotal_amount = $request->subTotal;
        $shipping->shipping_amount = $request->shippingAmount;
        $shipping->total_amount = $request->total;
        $shipping->region = $request->region;
        $shipping->city = $request->city;
        $shipping->delivery_address = $request->deliveryAddress;
        $shipping->user_id = $authId;
        $shipping->update();
        if ($shipping) {
            //create order
            if ($request->isEditShipping) {
                $order = Order::findOrFail($request->orderId);
                $order->address_id = $request->addressId;
                $order->pickup_location_id = $request->picLocId;
                $order->total = $request->total;
                $order->shipping_id = $shipping->id;
                $order->update();
            } else {
                $order = new Order();
                $order->order_no = mt_rand(1000000000000, 9999999999999);
                $order->address_id = $request->addressId;
                $order->pickup_location_id = $request->picLocId;
                $order->total = $request->total;
                $order->order_status_id = 1; //pending status
                $order->cart = json_encode($request->cart);
                $order->cart_thumb = $request->cart[0]['ProductThumb'];
                $order->customer_id = $authId;
                $order->shipping_id = $shipping->id;
                $order->billing_id = null;
                $order->save();
            }
        }
        return response()->json(['shipping' => new ShippingResource($shipping), 'order' => new OrderResource($order)]);
    }
}
