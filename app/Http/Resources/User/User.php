<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Address\PickupLocationCollection;
use App\Models\PickupLocation;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $pIds = [];
        if (count($this->pickups) > 0) {
            foreach ($this->pickups as $p) {
                $pIds[] = $p->id;
            }
        }
        $pickups = PickupLocation::whereIn('id', $pIds)->orderBy('created_at', 'desc')->get();

        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'pickups' => new PickupLocationCollection($pickups),
        ];
    }
}
