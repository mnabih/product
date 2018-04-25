<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\MoneyAccount;
use Session;

class MoneyAccountsController extends Controller
{
    #money accounts page
    public function MoneyAccountsPage()
    {
    	$accounts = MoneyAccount::with('User')->get();
    	return view('dashboard.money_accounts.money_accounts',compact('accounts',$accounts));
    }

    #accept
    public function Accept(Request $request)
    {
    	$user = User::findOrFail($request->id);
    	$user->arrears = $user->arrears - $request->ammount;
    	$user->save();
    	$money = MoneyAccount::findOrFail($request->money_id);
    	$money->status = 1;
    	$money->save();
    	Session::flash('success','تم تأكيد المعامله');
    	return back();
    }

    #accept and delete
    public function AcceptAndDelete(Request $request)
    {
    	$user = User::findOrFail($request->id);
    	$user->arrears = $user->arrears - $request->ammount;
    	$user->save();
    	$money = MoneyAccount::findOrFail($request->money_id);
    	MoneyAccount::findOrFail($request->money_id)->delete();
    	Session::flash('success','تم تأكيد المعامله مع الحف');
    	return back();
    }

    #delete
    public function Delete(Request $request)
    {
		MoneyAccount::findOrFail($request->money_id)->delete();
    	Session::flash('success','تم الحذذف');
    	return back();
    }
}
