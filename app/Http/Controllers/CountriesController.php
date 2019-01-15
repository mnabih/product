<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Country;
use Image;
use File;
use Session;

class CountriesController extends Controller
{
    #countries page
    public function countries()
    {
        $countries = Country::get();
        return view('dashboard.countries.countries',compact('countries'));
    }

    #add
    public function addCountry(Request $request)
    {
        $this->validate($request,[
            'name_ar'     =>'required',
        ],[
            'name_ar.required' => 'الاسم مطلوب'
        ]);

        $add=new Country;
        $add->name_ar       =$request->name_ar;
        $add->save();

        Report(Auth::user()->id,'بأضافة دولة جديد' . $add->name_ar);
        Session::flash('success','تم اضافة الدولة');
        return back();
    }

    #update
    public function updateCountry(Request $request)
    {
        $this->validate($request,[
            'edit_name_ar'     =>'required|max:190',
        ],[
            'edit_name_ar.required' => 'الاسم مطلوب'
        ]);

        $edit=Country::findOrFail($request->id);
        $edit->name_ar       =$request->edit_name_ar;
        $edit->save();


        Report(Auth::user()->id,'بتحديث بيانات الدولة  '.$edit->name_ar);
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #delete
    public function deleteCountry(Request $request)
    {

            $country = Country::findOrFail($request->id);
            $country->delete();

            Report(Auth::user()->id,'بحذف الدولة '.$country->name_ar);
            Session::flash('success','تم الحذف');
            return back();

    }


}
