<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'product_category');
    }

    public function subcategory()
    {
        return $this->hasMany('App\Models\Category', 'parent_id');
    }
}
