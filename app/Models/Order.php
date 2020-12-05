<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function customer()
    {
        return $this->belongsTo('App\User');
    }

    public function shipping()
    {
        return $this->belongsTo('App\Models\Shipping');
    }

    public function address()
    {
        return $this->belongsTo('App\Models\Address');
    }

    public function billing()
    {
        return $this->belongsTo('App\Models\Billing');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\OrderStatus', 'order_status_id');
    }

    public function charge()
    {
        return $this->belongsTo('App\Models\Charge', 'payment_id');
    }

    public function pickups()
    {
        return $this->belongsTo('App\Models\PickupLocation', 'pickup_location_id');
    }
}
