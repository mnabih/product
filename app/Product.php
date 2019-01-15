<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product extends Model
{
            use SoftDeletes;

	
    public function provider(){
    	return $this->belongsTo('App\User','user_id','id');
    }

    public function type(){
    	return $this->belongsTo('App\Type','type_id','id');
    }

    public function offers(){
    	return $this->hasMany('App\Offer','product_id','id');
    }

    public function images(){
        return $this->hasMany('App\Image');
    }

}
