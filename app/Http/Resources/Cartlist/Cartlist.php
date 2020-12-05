<?php

namespace App\Http\Resources\Cartlist;

use Illuminate\Http\Resources\Json\JsonResource;

class Cartlist extends JsonResource
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
            'Description' => $this->Description,
            'Media' => json_decode($this->Media),
            'Price' => $this->Price,
            'ProductName' => $this->ProductName,
            'ProductThumb' => $this->ProductThumb,
            'Quantity' => $this->Quantity,
            'SKU' => $this->SKU,
            'TotalPrice' => $this->TotalPrice,
            'ShippingAmount' => 0,
            'user' => $this->user,
        ];
    }
}
