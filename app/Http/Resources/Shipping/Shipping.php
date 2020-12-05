<?php

namespace App\Http\Resources\Shipping;

use Illuminate\Http\Resources\Json\JsonResource;

class Shipping extends JsonResource
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
            'city' => $this->city,
            'region' => $this->region,
            'shippingAmount' => $this->shipping_amount,
            'shippingMethod' => $this->shipping_method,
            'subtotalAmount' => $this->subtotal_amount,
            'totalAmount' => $this->total_amount,
            'user' => $this->user_id,
        ];
    }
}
