<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    public function orders()
    {
        return $this->hasMany('App\Models\Orders');
    }
}
