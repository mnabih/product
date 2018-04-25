<?php

namespace App\Http\Controllers;
use App\Report;
use Illuminate\Http\Request;
use Session;

class ReportsController extends Controller
{
    #reports page
    public function ReportsPage()
    {
        $usersReports      = Report::where('supervisor','0')->with('User')->latest()->paginate(40);
    	$supervisorReports = Report::where('supervisor','1')->with('User.Role')->latest()->paginate(40);
    	return view('dashboard.reports.reports',
        compact('usersReports',$usersReports,'supervisorReports',$supervisorReports));
    }

    #delete users reports 
    public function DeleteUsersReports()
    {
        $usersReports = Report::where('supervisor','0')->get();
		foreach ($usersReports  as $r)
		{
			$r->delete();
		}
		Session::flash('success','تم الحذف');
		return back();
    }

    #delete supervisors reports 
    public function DeleteSupervisorsReports()
    {
        $supervisorReports = Report::where('supervisor','1')->get();
        foreach ($supervisorReports  as $r)
        {
            $r->delete();
        }
        Session::flash('success','تم الحذف');
        return back();
    }
}
