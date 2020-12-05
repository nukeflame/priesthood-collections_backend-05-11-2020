<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cartlist extends Model
{
    protected $table = 'cart_lists';
    
    public function user()
    {
        return $this->belongsTo('App\User', 'userId');
    }
}
