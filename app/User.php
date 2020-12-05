<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'user_roles');
    }

    public function cartlist()
    {
        return $this->hasMany('App\Models\Cartlist', 'userId');
    }

    public function shipping()
    {
        return $this->hasMany('App\Models\Shipping');
    }

    public function address()
    {
        return $this->hasMany('App\Models\Address');
    }

    public function customers()
    {
        return $this->roles()->wherePivot('role_id', 4);
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'customer_id');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post', 'post_author');
    }

    public function verifyUser()
    {
        return $this->hasOne('App\Models\VerifyUser');
    }

    public function pickups()
    {
        return $this->belongsToMany('App\Models\PickupLocation', 'user_pickup_locations')->withPivot(['default_pickup', 'created_at', 'updated_at'])->orderBy('created_at', 'desc');
    }
}
