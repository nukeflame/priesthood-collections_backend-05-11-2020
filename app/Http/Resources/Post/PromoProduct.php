<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Product\ProductCollection;

class PromoProduct extends JsonResource
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
            'productName' => $this->product_name,
            'productInfo' => $this->product_info,
            'tags' => $this->tags,
            'productPrice' => $this->product_price,
            'postId' => $this->post_id,
            'slug' => $this->slug,
            'displayImage' => $this->product_avatar,
            'items' => new ProductCollection($this->items),
        ];
    }
}
