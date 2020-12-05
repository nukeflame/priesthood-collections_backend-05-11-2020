<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Billing;
use App\Models\Order;
use App\Http\Resources\Order\Order as OrderResource;
use App\Http\Resources\Billing\Billing as BillingResource;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //create billing
        $authId = Auth::id();
        $billing = new Billing();
        $billing->payment_method = $request->paymentMmethod;
        $billing->idf_no = Str::random();
        $billing->total_fee = $request->totalFee;
        $billing->vat = $request->vat;
        $billing->user_id = $authId;
        $billing->save();
        //link to order
        if ($billing) {
            $order = Order::findOrFail($request->orderId);
            $order->billing_id = $billing->id;
            $order->order_status_id = 2;
            $order->update();
        }

        return response()->json(['billing' => new BillingResource($billing), 'order' => new OrderResource($order)]);
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
        $billing = Billing::findOrFail($id);
        $billing->payment_method = $request->paymentMmethod;
        $billing->total_fee = $request->totalFee;
        $billing->vat = $request->vat;
        $billing->update();
        //update to order
        if ($billing) {
            $order = Order::findOrFail($request->orderId);
            $order->order_status_id = 2;
            $order->update();
        }

        return response()->json(['billing' => new BillingResource($billing), 'order' => new OrderResource($order)]);
    }
}
