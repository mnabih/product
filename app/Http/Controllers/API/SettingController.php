<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;
use App\City;
use Auth;
use File;
use App\User;
use App\SiteSetting;
use App\Social;
use Illuminate\Support\Facades\Validator;


class SettingController extends Controller
{
    public function __construct()
    {
    }

    public function phoneKeys(){
        $keys = Country::pluck('code');
        return response()->json(['value' => '1' , 'key' => 'success' , 'data' => $keys , 'code' => 200]);
    }

    #  App Info
    public function appInfo(Request $request)
    {        
        $info = SiteSetting::find(1);
            $data = [
                'name' => $info->site_name,
                'logo' => url('public/dashboard/uploads/setting/site_logo' . '/' . $info->site_logo),
            ];
        return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
    }

    # about App
    public function AboutApp(Request $request)
    {
        $info = SiteSetting::find(1);
            $data = [
                'aboutApp' => $info->site_description,
                'message' => $info->site_tagged,
                'vission' => $info->site_copyrigth,
            ];
        return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
    }

    # social Links
    public function socialLinks()
    {
        $Links = Social::get();
        if (count($Links) > 0) {
            $data = [];
            foreach ($Links as $link) {
                $data[] = [
                    'name' => $link->name,
                    'link' => $link->link,
                    'logo' => url('public/dashboard\uploads\socialicon' . '/' . $link->logo),
                ];
            }
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        } else {
            return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
        }
    }

    # all countries
    public function countries()
    {
        $countries = Country::get();
        if (count($countries) > 0) {
            $data = [];
            foreach ($countries as $country) {
                $data[] = [
                    'id' => $country->id,
                    'name' => $country->name_ar,
                ];
            }
            return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
        } else {
            return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
        }
    }

    # country cities
    public function countryCities(Request $request)
    {

        $validator        = Validator::make($request->all(), [
            'country_id'       => 'required|exists:countries,id',
        ]);
        if ($validator->passes()) {
            $cities = City::where('country_id', request('country_id'))->get();
            if (count($cities) > 0) {
                $data = [];
                foreach ($cities as $city) {                    
                    $data[] = [
                        'id' => $city->id,
                        'name' => $city->name_ar,                        
                    ];
                }
                return response()->json(['key' => 'success', 'value' => 1, 'data' => $data]);
            } else {
                return response()->json(['key' => 'success', 'value' => 1, 'data' => ""]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }
}
