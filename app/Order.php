<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function user(){
    	return $this->belongsTo('App\User', 'user_id','id');
    }

    public function delivery(){
        return $this->belongsTo('App\User', 'delivery_id','id');
    }

    public function orderDetails(){
        return $this->hasMany('App\Orderdetail');
    }
}
