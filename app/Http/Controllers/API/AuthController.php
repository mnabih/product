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

    public function signUp(Request $request)
    {
        $validator          = Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => 'required',
            'phone'         => 'required',
            'code'          => 'required',
            'password'      => 'required',
            'device_id'     => 'required|unique:users',
        ], [
            'device_id.required'    => 'deviceId is required',
            'device_id.unique'      => 'deviceId Exists',
        ]);

        if ($validator->passes()) {
            $number         = convert2english(request('phone'));
            $phone          = ltrim($number, '0');

            $Unique         = is_unique('phone', request('code') . $phone);
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
                'name'      => request('name'),
                'email'     => request('email'),
                'phone'     => request('code') . $phone,
                'code'      => request('code'),
                'active'    => 1,
                'mobile'    => request('phone'),
                'password'  => Hash::make(request('password')),
                'device_id' => request('device_id'),
            ]);

            $data = [
                'id'        => $user->id,
                'name'      => $user->name,
                'phone'     => $user->phone,
                'email'     => $user->email,
                'avatar'    => url('dashboard/uploads/users/'.$user->avatar),
                'device_id' => $user->device_id,
                'date'      => date_format(date_create($user->created_at), 'Y-m-d'),
            ];

            return response()->json(['value' => '1', 'key' => 'success', 'data' => $data, 'code' => 200]);

        } else {
            foreach ((array)$validator->errors() as $error) {
                {
                    if (isset($error['device_id'])) {
                        $msg = 'deviceId Exists';
                    } else {
                        $msg = $validator->errors();
                    }
                }
                return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $msg, 'code' => 401]);
            }
        }
    }

    public function signIn(Request $request)
    {
        $validator        = Validator::make($request->all(), [
            'phone'       => 'required',
            'password'    => 'required',
            'device_id'   => 'required',
        ]);

        if ($validator->passes()) {
            if (Auth::attempt(['phone' => request('phone'), 'password' => request('password')])) {

                if (Auth::user()->banned == 1)
                    return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'هذا الحساب محظور', 'code' => 401]);

                if (Auth::user()->active == 0)
                    return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'هذا الحساب فى إنتظار التفعيل', 'code' => 401]);

                    $user  = User::find(Auth::user()->id);
                    $user->device_id = request('device_id');
                    $user->save();
                    $data  = [
                        'id'            => $user->id,
                        'name'          => $user->name,
                        'email'         => $user->email,
                        'phone'         => $user->phone,
                        'avatar'        => url('dashboard/uploads/users/'.$user->avatar),
                        'device_id'     => $user->device_id,
                        'date'          => date_format(date_create($user->created_at), 'Y-m-d'),
                    ];

                    return response()->json(['value' => '1', 'key' => 'success', 'data' => $data , 'code' => 200]);
                }else {
                    return response()->json(['value' => '0', 'key' => 'fail', 'msg' => 'password or phone invalid' ,'code' => 401]);
             }
        }else {
            return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors(), 'code' => 401]);
        }
    }

    public function forgetPassword(Request $request)
    {
        $validator                     = Validator::make($request->all(),[
            'phone'                    => 'required'
        ]);

        if($validator->passes())
        {
            $user            = User::where('phone', request('phone'))->first();
            $msg             = 'كود التفعيل الخاص بك من أٌوكشن : ';
            if( $user ) {
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

    public function updatePassword(Request $request){
        $validator           = Validator::make($request->all(),[
            'user_id'        => 'required|exists:users,id',
            'code'           => 'required',
            'password'       => 'required',
        ],[
            'user_id.required'  => 'userId is required',
            'user_id.exists'    => 'userId Not Found'
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
                if(isset($error['user_id'])){
                    $msg = $error['user_id'][0];
                }else{
                    $msg  = $validator->errors();
                }
                return response()->json(['value' => '0' ,'key' => 'fail' ,'msg' => $msg , 'code' => 401]);
            }
        }
    }

    public function editProfile(Request $request)
    {
        $validator        = Validator::make($request->all(),[
            'phone'       => 'nullable|unique:users,phone,'   . $request->user_id,
            'email'       => 'nullable|unique:users,email,'   . $request->user_id,
            'avatar'      => 'nullable',
            'name'        => 'nullable',
            'code'        => 'nullable'
        ]);

        if($validator->passes())
        {
            $user         = User::find(request('user_id'));
            $number       = convert2english(request('phone'));
            $mobile       = ltrim($number, request('code'));

            if(request('phone'))
            {
                $user->phone  = $number ;
                $user->code   = request('code');
                $user->mobile = $mobile;
            }
            (request('email') != null) ? $user->email = request('email') : '';
            (request('name')  != null) ? $user->name  = request('name')  : '';

            if(request('avatar'))
            {
                if($user->avatar != 'default.png')
                {
                    File::delete('dashboard/uploads/users/'.$user->avatar);
                }
                $base64_img = request('avatar');
                $path =  'dashboard/uploads/users/';
                $fileName = upload_img($base64_img,$path);
                $user->avatar = $fileName;
            }
            $user->save();

            $data  = [
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'phone'         => $user->phone,
                'avatar'        => url('dashboard/uploads/users/'.$user->avatar),
                'device_id'     => $user->device_id,
                'date'          => date_format(date_create($user->created_at), 'Y-m-d'),
            ];
            return response()->json(['value' => '1' , 'key' => 'success' , 'data' => $data , 'code' => 200]);
        }else{
            foreach ((array) $validator->errors() as $error)
            {
                if(isset($error['phone']))
                {
                    $msg   = $error['phone'][0];
                }elseif(isset($error['email']))
                {
                    $msg   = $error['email'][0];
                }else{
                    $msg   = $validator->errors();
                }
                return response()->json(['value' => '0' , 'key' => 'fail' , 'msg' => $msg , 'code' => 401]);
            }
        }
    }

    public function editPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'           => 'required',
            'current_password'  => 'required',
            'password'          => 'required',
        ]);

        if ($validator->passes()){

            $user               = User::find(request('user_id'));
            if (Hash::check(request('current_password'), $user->password)) {

                $user->password = Hash::make(request('password'));

                if($user->save())
                 $msg =  'تم تعديل كلمة المرور بنجاح' ;
                return response()->json(['key' => '1', 'value' => 'success', 'msg' => $msg]);

            }else{
                $msg =   'كلمة المرور الحالية غير صحيحه' ;
                return response()->json(['key' => '0', 'value' => 'fail', 'msg' => $msg]);
            }
        }else{
            foreach ((array)$validator->errors() as $error) {
                    return response()->json(['value' => '0', 'key' => 'fail', 'msg' => $validator->errors()]);
            }
        }
    }
}