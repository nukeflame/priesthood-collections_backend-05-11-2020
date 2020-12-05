<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateRegion extends Model
{
    public function addresses()
    {
        return $this->hasMany('App\Models\Address', 'state_region_id');
    }
}
