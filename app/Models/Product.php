<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    
    public function media()
    {
        return $this->belongsToMany('App\Models\Media', 'product_media', 'product_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'product_category');
    }


    public function promoItems()
    {
        return $this->belongsToMany('App\Models\PromoProduct', 'promo_product_items');
    }

    public function stock()
    {
        return $this->hasOne('App\Models\Stock');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }
}
