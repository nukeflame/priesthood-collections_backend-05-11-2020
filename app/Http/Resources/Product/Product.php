<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Media\MediaCollection;

class Product extends JsonResource
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
            'productName' => $this->name,
            'price' => $this->price,
            'brandName' => $this->brand_name,
            'category' => $this->category,
            'shippingPrice' => $this->shipping_price,
            'sku' => $this->sku,
            'description' => $this->description,
            'specifications' => $this->specifications,
            'packaging' => $this->packaging,
            'media' => new MediaCollection($this->media),
            'comparePrice' => $this->compare_price,
            'productThumb' => $this->product_thumbnail,
            'inventory' => $this->stock,
            'brand' => $this->brand,
            'qty' => $this->qty,
        ];
    }
}
