<?php

namespace App\Http\Resources\Billing;

use Illuminate\Http\Resources\Json\JsonResource;

class Billing extends JsonResource
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
            'paymentMethod' => $this->payment_method,
            'totalFee' => $this->total_fee,
            'vat' => $this->vat,
            'coupon' => $this->coupon_id,
            'mobileNo' => $this->mobile_no,
            'user' => $this->user_id,
            'idfNo' => $this->idf_no,
            'errDesc' => $this->error_desc,
        ];
    }
}
