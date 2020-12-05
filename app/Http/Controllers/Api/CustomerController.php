<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Customer\CustomerRequest;
use App\Models\Address;
use App\User;
use Hash;
use App\Http\Resources\User\Customer as CustomerResource;
use App\Http\Resources\User\CustomerCollection;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = User::has('customers')->orderBy('created_at', 'desc')->get();
        return new CustomerCollection($customers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $user = new User();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->access_portal = $request->accessPortal;
        $user->save();
        //attach roles
        $roleId = $request->hasNews ? [4,5] : 4;
        $user->roles()->attach($roleId);
        //address details
        if ($user) {
            try {
                $address = new Address();
                $address->delivery_address = $request->deliveryAddress;
                $address->firstname = ucfirst($request->firstname);
                $address->lastname = ucfirst($request->lastname);
                $address->mobile_no = "+254" .  $request->mobileNo;
                $address->other_mobile_no	 = $request->otherMobileNo ? "+254" . $request->otherMobileNo : null;
                $address->state_region_id = $request->stateRegion;
                $address->city_id = $request->city;
                $address->user_id = $user->id;
                $address->save();
            } catch (\Throwable $th) {
                $user->delete();
                $user->roles()->detach();
                return response()->json(['error404', 'An error occured please try again'], 404);
            }
        }

        return  new CustomerResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$id) {
            return;
        }
        $customer = User::where('id', $id)->has('customers')->first();
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->password) {
            $user->password  = Hash::make($request->password);
        }
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->access_portal = $request->accessPortal;
        $user->update();
        //attach roles
        $roleId = $request->hasNews ? [4,5] : 4;
        $user->roles()->attach($roleId);
        //address details
        if ($user) {
            try {
                $address = Address::find($request->addressId);
                if (!empty($address)) {
                    $address->delivery_address = $request->deliveryAddress;
                    $address->firstname = ucfirst($request->firstname);
                    $address->lastname = ucfirst($request->lastname);
                    $address->mobile_no = "+254" .  $request->mobileNo;
                    $address->other_mobile_no	 = $request->otherMobileNo ? "+254" . $request->otherMobileNo : null;
                    $address->state_region_id = $request->stateRegion;
                    $address->city_id = $request->city;
                    $address->user_id = $user->id;
                    $address->update();
                } else {
                    $address = new  Address();
                    $address->delivery_address = $request->deliveryAddress;
                    $address->firstname = ucfirst($request->firstname);
                    $address->lastname = ucfirst($request->lastname);
                    $address->mobile_no = "+254" .  $request->mobileNo;
                    $address->other_mobile_no	 = $request->otherMobileNo ? "+254" . $request->otherMobileNo : null;
                    $address->state_region_id = $request->stateRegion;
                    $address->city_id = $request->city;
                    $address->user_id = $user->id;
                    $address->save();
                }
            } catch (\Throwable $th) {
                return response()->json(['error404', 'An error occured please try again'], 404);
            }

            return new CustomerResource($user);
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
        //
    }
}
