<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoneyAccount extends Model
{
    protected $table = 'money_accounts';

    public function User()
    {
    	return $this->belongsTo('App\User','user_id','id');
    }
}
