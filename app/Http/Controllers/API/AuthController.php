<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Validator;
use Session;
use File;
use Hash;
class AuthController extends Controller
{
    public function __construct()
    {
    }

    /********************************** start new project work *******************************/

    /*********************** provider *************/

    public function provider_register(Request $request)
    {

        $validator          = Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => 'required|email|unique:users',
            'phone'         => 'required|numeric|digits:10|unique:users',
            'country_id'    => 'required|exists:countries,id',
            'city_id'       => 'required|exists:cities,id',
            'password'      => 'required',
            'device_id'     => 'required',
        ], [
            
        ]);

        if ($validator->passes()) {
            $number         = convert2english(request('phone'));
            //$phone          = ltrim($number, '0');

            $Unique         = is_unique('phone', $number);
            if ($Unique)
            {
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'هذا الجوال مستخدم مسبقآ', 'code' => 401]);
            }

            $Unique = is_unique('email', request('email'));
            if ($Unique)
            {
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'هذا البريد مستخدم مسبقآ', 'code' => 401]);
            }
            $user   = User::create([
                'name'          => request('name'),
                'email'         => request('email'),
                'phone'         => $number,
                'country_id'    => request('country_id'),
                'city_id'       => request('city_id'),
                'active'        => 1,
                'is_provider'   => 1,
                'password'      => Hash::make(request('password')),
                'device_id'     => request('device_id'),
            ]);

            $data = [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => isset($user->email)? $user->email: "",
                'code'              => isset($user->code)? $user->code: "",
                'phone'             => $user->phone,
                'lang'              => $user->lang,
                'arrears'           => $user->arrears,
                'active'            => $user->active,
                'role'              => $user->role,
                'lat'               => isset($user->lat)? $user->lat: "",
                'lng'               => isset($user->lng)? $user->lng: "",
                'avatar'            => isset($user->avatar)? url('public/dashboard/uploads/users/'.$user->avatar):"",
                'device_id'         => isset($user->device_id)? $user->device_id: "",
                'is_provider'       => $user->is_provider,
                'country_id'        => isset($user->country_id)? $user->country_id: "",
                'city_id'           => isset($user->city_id)? $user->city_id: "",
            ];

            return response()->json(['value' => '1', 'key' => 'success', 'data' => $data, 'code' => 200]);

        } else {
            foreach ((array)$validator->errors() as $error) {
                {
                    $msg = $validator->errors();
                    
                }
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg, 'code' => 401]);
            }
        }
    }

    public function provider_login(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'phone'       => 'required',
            'password'    => 'required',
            'device_id'   => 'required',
        ]);
        if ($validator->passes()) {
            if (Auth::attempt(['phone' => request('phone'), 'password' => request('password')])) {            
                if (Auth::user()->active == 0)
                    return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'هذا الحساب فى إنتظار التفعيل', 'code' => 401]);

                    $user  = User::find(Auth::user()->id);
                    $user->device_id = request('device_id');
                    $user->save();
                    $data  = [

                        'id'                => $user->id,
                        'name'              => $user->name,
                        'email'             => isset($user->email)? $user->email: "",
                        'code'              => isset($user->code)? $user->code: "",
                        'phone'             => $user->phone,
                        'lang'              => $user->lang,
                        'arrears'           => $user->arrears,
                        'active'            => $user->active,
                        'role'              => $user->role,
                        'lat'               => isset($user->lat)? $user->lat: "",
                        'lng'               => isset($user->lng)? $user->lng: "",
                        'avatar'            => isset($user->avatar)? url('public/dashboard/uploads/users/'.$user->avatar):"",
                        'device_id'         => isset($user->device_id)? $user->device_id: "",
                        'is_provider'       => $user->is_provider,
                        'country_id'        => isset($user->country_id)? $user->country_id: "",
                        'city_id'           => isset($user->city_id)? $user->city_id: "",
                    ];
                    return response()->json(['value' => '1', 'key' => 'success', 'data' => $data , 'code' => 200]);

            }else {
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'رقم الهاتف او كلمة المرور غير صحيح' ,'code' => 401]);
            }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    public function providerForgetPassword(Request $request)
    {
        $validator                     = Validator::make($request->all(),[
            'phone'                    => 'required'
        ]);

        if($validator->passes())
        {
            $user            = User::where('phone', request('phone'))->first();
            $msg             = 'كود التفعيل الخاص بك من أٌوكشن : ';

            if($user->is_provider == 0){
                $msg  = 'الحساب خاص بعميل لايحتاج الى كلمة مرور';
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg , 'code' => 401]);
            }
            if( $user) {
                $user->code = generate_code();
                $user->save();
                $msg        = $msg . $user->code;
                $phone      = $user->mobile;

                send_mobile_sms($phone, $msg);

                $data = [
                    'id'    => $user->id,
                    'code'  => $user->code
                ];
                return response()->json(['value' => '1', 'key' => 'success', 'data' => $data , 'code' => 200]);
            }else{
                $msg  = 'لا يوجد حساب بهذا الرقم';
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg , 'code' => 401]);
            }
        }else{
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    public function providerUpdatePassword(Request $request)
    {
        $validator           = Validator::make($request->all(),[
            'user_id'        => 'required|exists:users,id',
            'code'           => 'required',
            'password'       => 'required',
        ],[
            
        ]);

        if($validator->passes()){
            $user           = User::find(request('user_id'));
            if($user->code != request('code')){
                $msg        =   'كود التحقق لا ينتمى لأى مستخدم';
                return response()->json(['value' => '0' ,'key' => 'fail' , 'msg' => $msg , 'code' => 401]);
            }

                $user->password = Hash::make(request('password'));
                $user->save();
                $msg        =    'تم تحديث كلمة المرور ';
                return response()->json(['value' => '1' , 'key' => 'success' ,'msg' => $msg , 'code' => 200]);

        }else{
            foreach ((array) $validator->errors() as $error){
                $msg  = $validator->errors();
                return response()->json(['value' => '0' ,'key' => 'fail' ,'msg' => $msg , 'code' => 401]);
            }
        }
    }

    public function providerEditProfile(Request $request)
    {
        $validator        = Validator::make($request->all(),[
            'user_id'     => 'required|exists:users,id',
            'phone'       => 'nullable|digits:10|unique:users,phone,'   . $request->user_id,
            'email'       => 'nullable|unique:users,email,'   . $request->user_id,
            'avatar'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'name'        => 'nullable',
            'city_id'     => 'nullable|exists:cities,id',
            'password'    => 'nullable',

            'lat'           => 'nullable',
            'lng'           => 'nullable',
            'country_id'    => 'nullable',
        ]);

        if($validator->passes())
        {
            $user         = User::find(request('user_id'));
            if($user->is_provider == 0){
                $msg  = 'الحساب خاص بعميل وليس مقدم خدمة';
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg , 'code' => 401]);
            }

            $number       = convert2english(request('phone'));
            //$mobile       = ltrim($number, request('code'));
            if(request('phone'))
            {
                $user->phone  = $number ;
                //$user->code   = request('code');
            }

            (request('email') != null) ? $user->email = request('email') : '';
            (request('name')  != null) ? $user->name  = request('name')  : '';

            (request('city_id')  != null) ?     $user->city_id  = request('city_id')  : '';
            (request('country_id')  != null) ?  $user->country_id  = request('country_id')  : '';
            (request('lat')  != null) ?         $user->lat  = request('lat')  : '';
            (request('lng')  != null) ?         $user->lng  = request('lng')  : '';
            (request('password')  != null) ?    $user->password  = Hash::make(request('password'))  : '';


            if(request('avatar'))
            {
                if($user->avatar != 'default.png')
                {
                    File::delete('public/dashboard/uploads/users/'.$user->avatar);
                }

                $image = request('avatar');
                $name=date('d-m-y').time().rand()  .'.'.$image->getClientOriginalExtension();
                $image->move('public/dashboard/uploads/users/', $name);
                $user->avatar = $name;
           
            }

            $user->save();

            $data  = [

                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => isset($user->email)? $user->email: "",
                'code'              => isset($user->code)? $user->code: "",
                'phone'             => $user->phone,
                'lang'              => $user->lang,
                'arrears'           => $user->arrears,
                'active'            => $user->active,
                'role'              => $user->role,
                'lat'               => isset($user->lat)? $user->lat: "",
                'lng'               => isset($user->lng)? $user->lng: "",
                'avatar'            => isset($user->avatar)? url('public/dashboard/uploads/users/'.$user->avatar):"",
                'device_id'         => isset($user->device_id)? $user->device_id: "",
                'is_provider'       => $user->is_provider,
                'country_id'        => isset($user->country_id)? $user->country_id: "",
                'city_id'           => isset($user->city_id)? $user->city_id: "",

            ];
            return response()->json(['value' => '1' , 'key' => 'success' , 'data' => $data , 'code' => 200]);
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    /************* end provider ***********/
    /************* customer **************/


    public function customer_register(Request $request)
    {

        $validator          = Validator::make($request->all(), [
            'name'          => 'required',
            'phone'         => 'required|numeric|digits:10|unique:users',
            'country_id'    => 'required|exists:countries,id',
            'city_id'       => 'required|exists:cities,id',
            'password'      => 'required',
            'lat'           => 'required',
            'lng'           => 'required',
            'device_id'     => 'required',
        ]);

        if ($validator->passes()) {
            $number         = convert2english(request('phone'));
            //$phone          = ltrim($number, '0');

            $Unique         = is_unique('phone', $number);
            if ($Unique)
            {
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'هذا الجوال مستخدم مسبقآ', 'code' => 401]);
            }

            
            $user   = User::create([
                'name'          => request('name'),
                'lat'           => request('lat'),
                'lng'           => request('lng'),
                'phone'         => $number,
                'country_id'    => request('country_id'),
                'city_id'       => request('city_id'),
                'active'        => 1,
                'is_provider'   => 0,
                'password'      => Hash::make(request('password')),
                'device_id'     => request('device_id'),
            ]);

            $data = [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => isset($user->email)? $user->email: "",
                'code'              => isset($user->code)? $user->code: "",
                'phone'             => $user->phone,
                'lang'              => $user->lang,
                'arrears'           => $user->arrears,
                'active'            => $user->active,
                'role'              => $user->role,
                'lat'               => isset($user->lat)? $user->lat: "",
                'lng'               => isset($user->lng)? $user->lng: "",
                'avatar'            => isset($user->avatar)? url('public/dashboard/uploads/users/'.$user->avatar):"",
                'device_id'         => isset($user->device_id)? $user->device_id: "",
                'is_provider'       => $user->is_provider,
                'country_id'        => isset($user->country_id)? $user->country_id: "",
                'city_id'           => isset($user->city_id)? $user->city_id: "",
            ];

            return response()->json(['value' => '1', 'key' => 'success', 'data' => $data, 'code' => 200]);

        } else {
            foreach ((array)$validator->errors() as $error) {
                {
                    $msg = $validator->errors();
                }
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg, 'code' => 401]);
            }
        }
    }

    public function customer_login(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'phone'       => 'required',
            'device_id'   => 'required',
        ]);

        if ($validator->passes()) {

                $user = User::where('phone', $request->get('phone'))->first();

                if(!$user){
                    return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'لا يوجد حساب لهذا الرقم', 'code' => 401]);

                }

                if($user->is_provider == 1){
                    return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'الحساب يخص مقدم خدمة - يجب ادخال كلمة المرور', 'code' => 401]);   
                }

                if ($user->active == 0)
                    return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'هذا الحساب فى إنتظار التفعيل', 'code' => 401]);

                \Auth::login($user);

                    $user  = User::find(Auth::user()->id);
                    $user->device_id = request('device_id');
                    $user->save();
                    $data  = [
                        'id'                => $user->id,
                        'name'              => $user->name,
                        'email'             => isset($user->email)? $user->email: "",
                        'code'              => isset($user->code)? $user->code: "",
                        'phone'             => $user->phone,
                        'lang'              => $user->lang,
                        'arrears'           => $user->arrears,
                        'active'            => $user->active,
                        'role'              => $user->role,
                        'lat'               => isset($user->lat)? $user->lat: "",
                        'lng'               => isset($user->lng)? $user->lng: "",
                        'avatar'            => isset($user->avatar)? url('public/dashboard/uploads/users/'.$user->avatar):"",
                        'device_id'         => isset($user->device_id)? $user->device_id: "",
                        'is_provider'       => $user->is_provider,
                        'country_id'        => isset($user->country_id)? $user->country_id: "",
                        'city_id'           => isset($user->city_id)? $user->city_id: "",
                    ];

                    return response()->json(['value' => '1', 'key' => 'success', 'data' => $data , 'code' => 200]);               
             
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    public function customerEditProfile(Request $request)
    {
        $validator        = Validator::make($request->all(),[
            'user_id'     => 'required|exists:users,id',
            'phone'       => 'nullable|digits:10|unique:users,phone,'   . $request->user_id,
            'avatar'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'name'        => 'nullable',
            'city_id'     => 'nullable|exists:cities,id',

            'lat'           => 'nullable',
            'lng'           => 'nullable',
            'country_id'    => 'nullable',
            'password'      => 'nullable',
        ]);

        if($validator->passes())
        {
            $user         = User::find(request('user_id'));

            if($user->is_provider == 1){
                $msg  = 'الحساب خاص بمقدم خدمة وليس عميل ';
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg , 'code' => 401]);
            }

            $number       = convert2english(request('phone'));
            //$mobile       = ltrim($number, request('code'));

            if(request('phone'))
            {
                $user->phone  = $number ;
                //$user->code   = request('code');
            }
            (request('name')  != null) ?        $user->name  = request('name')  : '';


            (request('city_id')  != null) ?     $user->city_id  = request('city_id')  : '';
            (request('country_id')  != null) ?  $user->country_id  = request('country_id')  : '';
            (request('lat')  != null) ?         $user->lat  = request('lat')  : '';
            (request('lng')  != null) ?         $user->lng  = request('lng')  : '';
            (request('password')  != null) ?    $user->password  = Hash::make(request('password'))  : '';

            if(request('avatar'))
            {
                if($user->avatar != 'default.png')
                {
                    File::delete('public/dashboard/uploads/users/'.$user->avatar);
                }

                $image = request('avatar');
                $name=date('d-m-y').time().rand()  .'.'.$image->getClientOriginalExtension();
                $image->move('public/dashboard/uploads/users/', $name);
                $user->avatar = $name;
           
            }

            $user->save();

            $data  = [


                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => isset($user->email)? $user->email: "",
                'code'              => isset($user->code)? $user->code: "",
                'phone'             => $user->phone,
                'lang'              => $user->lang,
                'arrears'           => $user->arrears,
                'active'            => $user->active,
                'role'              => $user->role,
                'lat'               => isset($user->lat)? $user->lat: "",
                'lng'               => isset($user->lng)? $user->lng: "",
                'avatar'            => isset($user->avatar)? url('public/dashboard/uploads/users/'.$user->avatar):"",
                'device_id'         => isset($user->device_id)? $user->device_id: "",
                'is_provider'       => $user->is_provider,
                'country_id'        => isset($user->country_id)? $user->country_id: "",
                'city_id'           => isset($user->city_id)? $user->city_id: "",

            ];
            return response()->json(['value' => '1' , 'key' => 'success' , 'data' => $data , 'code' => 200]);
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }


    /***** end customer **********/

    public function logOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'           => 'required|exists:users,id',
        ]);

        if ($validator->passes()){

            $user = User::find($request->user_id);            
            $user->device_id = NULL ;
            $user->update();

            Auth::logout();
            
            return response()->json(['value' => '1', 'key' => 'success']);
        }
        return response()->json(['key' => 'error', 'value' => 0, 'data' => $validator->errors()]);


    }

    public function activeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'           => 'required|exists:users,id',
        ]);

        if ($validator->passes()){

            $user = User::find($request->user_id);            
            if($user->active == 0){
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'هذا الحساب فى إنتظار التفعيل', 'code' => 401]);
            }
            
            return response()->json(['value' => '1', 'key' => 'success']);
        }
        return response()->json(['key' => 'error', 'value' => 0, 'data' => $validator->errors()]);


    }

    public function userData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'           => 'required|exists:users,id',
        ]);

        if ($validator->passes()){

            $user = User::find($request->user_id);
            //return $user;
            if($user->active == 0){
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'هذا الحساب فى إنتظار التفعيل', 'code' => 401]);
            }

            $data  = [

                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => isset($user->email)? $user->email: "",
                'code'              => isset($user->code)? $user->code: "",
                'phone'             => $user->phone,
                'lang'              => $user->lang,
                'arrears'           => $user->arrears,
                'active'            => $user->active,
                'role'              => $user->role,
                'lat'               => isset($user->lat)? $user->lat: "",
                'lng'               => isset($user->lng)? $user->lng: "",
                'avatar'            => isset($user->avatar)? url('public/dashboard/uploads/users/'.$user->avatar):"",
                'device_id'         => isset($user->device_id)? $user->device_id: "",
                'is_provider'       => $user->is_provider,
                'country_id'        => isset($user->country_id)? $user->country_id: "",
                'city_id'           => isset($user->city_id)? $user->city_id: "",

            ];

            return response()->json(['value' => '1', 'key' => 'success', 'data' => $data , 'code' => 200]);
        }
        return response()->json(['key' => 'error', 'value' => 0, 'data' => $validator->errors()]);


    }


    /************************ end new project work ***********************************/
}