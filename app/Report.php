<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
	protected $table = 'report';
	
	public function User()
	{
		return $this->belongsTo('App\User','user_id','id');
	}


}
