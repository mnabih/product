<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\City;
use App\Country;
use Image;
use File;
use Session;

class CitiesController extends Controller
{
    #cities page
    public function cities()
    {
        $cities = City::get();
        $countries = Country::get();
        return view('dashboard.cities.cities',compact('countries','cities'));
    }

    #add
    public function addCity(Request $request)
    {
        $this->validate($request,[
            'country_id'     =>'required|exists:countries,id',
            'name_ar'        =>'required',
        ],[
            'name_ar.required' => 'الاسم مطلوب',
            'country_id.required' => ' الدولة مطلوبة'
        ]);

        $add=new City;
        $add->country_id       =$request->country_id;
        $add->name_ar          =$request->name_ar;
        $add->save();

        Report(Auth::user()->id,'بأضافة مدينة جديدة - ' . $add->name_ar);
        Session::flash('success','تم اضافة المدينة');
        return back();
    }

    #update
    public function updateCity(Request $request)
    {
        $this->validate($request,[
            'edit_country_id'     =>'required|exists:countries,id',
            'edit_name_ar'        =>'required|max:190',
        ],[
            'edit_name_ar.required' => 'الاسم مطلوب',
            'edit_country_id.required' => ' الدولة مطلوبة'
        ]);

        $update =City::findOrFail($request->id);
        $update->country_id       =$request->edit_country_id;
        $update->name_ar          =$request->edit_name_ar;
        $update->save();


        Report(Auth::user()->id,'بتحديث بيانات مدينة -   '.$update->name_ar);
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #delete
    public function deleteCity(Request $request)
    {

        $city = City::findOrFail($request->id);
        $city->delete();

        Report(Auth::user()->id,'بحذف مدينة -  '.$city->name_ar);
        Session::flash('success','تم الحذف');
        return back();

    }


}
