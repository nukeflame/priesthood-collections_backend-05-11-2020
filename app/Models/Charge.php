<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $fillable = ['amount','currency','source','description'];

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'payment_id');
    }
}
