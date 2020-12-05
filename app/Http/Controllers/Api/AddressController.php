<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Http\Requests\Address\AddressRequest;
use App\User;
use App\Http\Resources\Address\Address as AddressResource;
use App\Models\Order;

class AddressController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddressRequest $request)
    {
        $address = new Address();
        $address->delivery_address = $request->deliveryAddress;
        $address->delivery_address = $request->deliveryAddress2;
        $address->firstname = ucfirst($request->firstname);
        $address->lastname = ucfirst($request->lastname);
        $address->mobile_no = "+254" .  $request->mobileNo;
        $address->other_mobile_no     = $request->otherMobileNo ? "+254" . $request->otherMobileNo : null;
        $address->state_region_id = $request->stateRegion;
        $address->city_id = $request->city;
        $address->user_id = Auth::id();
        $address->save();
        //order
        if ($address) {
            $order = new Order();
            $order->order_no = mt_rand(100000000, 999999999);
            $order->address_id = $address->id;
            $order->cart = json_encode($request->cart);
            $order->payment_id = 0;
            $order->total = 0;
            $order->order_status_id = 1;
            $order->customer_id = Auth::id();
            $order->shipping_id = 0;
            $order->billing_id = 0;
            $order->save();
        }
        return new AddressResource($address);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $address = Address::where('user_id', $user->id)->latest()->first();
        if (empty($address)) {
            return response()->json([]);
        }
        return new AddressResource($address);
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
        $address =  Address::findOrFail($id);
        $address->delivery_address = $request->deliveryAddress;
        $address->delivery_address2 = $request->deliveryAddress2;
        $address->firstname = ucfirst($request->firstname);
        $address->lastname = ucfirst($request->lastname);
        $address->mobile_no = "+254" . $request->mobileNo;
        $address->other_mobile_no = $request->otherMobileNo ? "+254" .  $request->otherMobileNo : null;
        $address->state_region_id = $request->stateRegion;
        $address->city_id = $request->city;
        $address->user_id = Auth::id();
        $address->update();

        return new AddressResource($address);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
