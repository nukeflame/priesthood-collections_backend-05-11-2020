<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public function order()
    {
        return $this->hasOne('App\Models\Order');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function region()
    {
        return $this->belongsTo('App\Models\StateRegion', 'state_region_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
