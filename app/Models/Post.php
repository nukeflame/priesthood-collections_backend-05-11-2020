<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function author()
    {
        return $this->belongsTo('App\User', 'post_author');
    }

    public function promoProducts()
    {
        return $this->hasMany('App\Models\PromoProduct', 'post_id');
    }
}
