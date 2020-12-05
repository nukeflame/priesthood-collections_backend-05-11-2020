<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupLocation extends Model
{
    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function region()
    {
        return $this->belongsTo('App\Models\StateRegion', 'state_region_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_pickup_locations')->withPivot(['default_pickup', 'created_at', 'updated_at'])->orderBy('created_at', 'desc');
    }

    public function order()
    {
        return $this->hasOne('App\Models\Order');
    }
}
