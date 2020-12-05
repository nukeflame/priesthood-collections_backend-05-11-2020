<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function addresses()
    {
        return $this->hasMany('App\Models\Address');
    }

    public function regions()
    {
        return $this->belongsToMany('App\Models\Region', 'city_regions');
    }
}
