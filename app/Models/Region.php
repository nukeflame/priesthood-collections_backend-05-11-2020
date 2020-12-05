<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = "state_regions";

    public function city()
    {
        return $this->belongsToMany('App\Models\City', 'city_regions');
    }
}
