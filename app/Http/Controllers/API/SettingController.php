<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;
use Auth;
use File;
use App\User;
class SettingController extends Controller
{
    public function __construct()
    {
    }

    public function phoneKeys(){
        $keys = Country::pluck('code');
        return response()->json(['value' => '1' , 'key' => 'success' , 'data' => $keys , 'code' => 200]);
    }
}
