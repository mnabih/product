<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Type extends Model
{
	    use SoftDeletes;

    public function products(){
        return $this->hasMany('App\Product');
    }
}
