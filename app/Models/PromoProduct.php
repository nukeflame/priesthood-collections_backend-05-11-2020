<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoProduct extends Model
{
    public function post()
    {
        return $this->belongsTo('App\Models\Post', 'post_id');
    }

    public function offerdItems()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function items()
    {
        return $this->belongsToMany('App\Models\Product', 'promo_product_items');
    }
}
