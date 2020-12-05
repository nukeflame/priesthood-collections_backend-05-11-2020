<?php

namespace App\Http\Resources\Address;

use Illuminate\Http\Resources\Json\JsonResource;
use  App\Http\Resources\Order\Order as OrderResource;
use App\Http\Resources\Region\StateRegion as StateRegionResource;
use App\Http\Resources\City\City as CityResource;

class Address extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'deliveryAddress' => $this->delivery_address,
            'deliveryAddress2' => $this->delivery_address2,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'mobileNo' => $this->mobile_no,
            'otherMobileNo' => $this->other_mobile_no,
            'stateRegion' => new StateRegionResource($this->region),
            'city' => new CityResource($this->city),
            'user' => $this->user_id,
            'order' => new OrderResource($this->order),
        ];
    }
}
