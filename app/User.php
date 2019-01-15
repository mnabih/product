<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','mobile','code','device_id','country_id','city_id','is_provider','lat','lng','active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function Role()
    {
        return $this->hasOne('App\Role','id','role');
    }

    public function Reports()
    {
        return $this->hasMany('App\Report','user_id','id');
    }

    public function city()
    {
        return $this->hasOne('App\City','id','city_id');
    }

    public function country()
    {
        return $this->hasOne('App\Country','id','country_id');
    }

    

}
