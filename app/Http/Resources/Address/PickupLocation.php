<?php

namespace App\Http\Resources\Address;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\City\City as CityResource;
use App\Http\Resources\Region\StateRegion as RegionResource;

class PickupLocation extends JsonResource
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
            'address' => $this->address,
            'city' => new CityResource($this->city),
            'region' => new RegionResource($this->region),
            'owner' => $this->user,
        ];
    }
}
