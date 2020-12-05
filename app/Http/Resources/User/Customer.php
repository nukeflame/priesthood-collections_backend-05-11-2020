<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order\OrderCollection;
use  App\Http\Resources\City\City as CityResource;
use  App\Http\Resources\Region\StateRegion as StateRegionResource;

class Customer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $mobileNo = null;
        $otherMobileNo = null;
        $deliveryAddress = "";
        $region = null;
        $city = null;
        $addressId = null;
        $address = $this->address;
        if (count($address) > 0) {
            $d = $address->first();
            $addressId = $d->id;
            $mobileNo = $d->mobile_no;
            $otherMobileNo = $d->other_mobile_no;
            $deliveryAddress = $d->delivery_address;
            $region =  new StateRegionResource($d->region);
            $city = new CityResource($d->city);
        }

        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'accessPortal' => $this->access_portal,
            'email' => $this->email,
            'mobileNo' => $mobileNo,
            'otherMobileNo' => $otherMobileNo,
            'deliveryAddress' => $deliveryAddress,
            'region' => $region,
            'city' => $city,
            'lastSeen' => '6/1/2020',
            'orders' => new OrderCollection($this->orders),
            'totalSpent' => '0.00 Ksh',
            'latestPurchase' => '11/18/2019, 3:59:26 AM',
            'hasNews' => true,
            'roles' => 'Subscriber',
            'addressId' => $addressId
        ];
    }
}
