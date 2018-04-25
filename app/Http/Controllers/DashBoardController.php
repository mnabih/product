<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use View;
use Session;


class DashBoardController extends Controller
{
    public function Index()
    {
    	return view('dashboard.parts.main');
    }
}
