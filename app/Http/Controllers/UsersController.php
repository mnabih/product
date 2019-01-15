<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\PublicMessage;
use App\SmsEmailNotification;
use Mail;
use Auth;
use App\User;
use App\Role;
use App\Order;
use App\Country;
use App\City;
use Image;
use File;
use Session;

class UsersController extends Controller
{
    #users page
    public function Users()
    {
        $users = User::with('Role')->latest()->get();
        $roles = Role::latest()->get();
        $countries = Country::get();
        $cities = City::get();
        return view('dashboard.users.users',compact('users','roles','countries','cities'));
    }

    #add user
    public function AddUser(Request $request)
    {
        $this->validate($request,[

            'name'          =>'required|min:2|max:190',
            'email'         =>'nullable|email|min:2|max:190|unique:users',
            'phone'         =>'required|min:2|max:190|digits:10|unique:users',
            'password'      =>'required|min:6|max:190',
            'country_id'    =>'required|exists:countries,id',
            'city_id'       =>'required|exists:cities,id',
            'avatar'        =>'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ],[
            'name.required'=>'الاسم مطلوب',
            'email.required'=>'الايميل مطلوب',
            'email.email'=>'الايميل  يجب ان يكون من نوع ايميل',
            'phone.required'=>'رقم الجوال مطلوب',
            'phone.unique:users'=>'رقم الجوال مستخدم من قبل',
            'password.required'=>'كلمة المرور مطلوبة',
            'country_id.required'=>'الدولة مطلوبة',
            'city_id.required'=>'المدينة مطلوبة',
            'avatar.image'=>'الصورة يجب ان تكون من نوع صورة',
        ]);

        $user=new User;
        $user->name             =$request->name;
        $user->email            =$request->email;
        $user->phone            =$request->phone;
        $user->role             =$request->role?$request->role:0 ;
        $user->country_id       =$request->country_id;
        $user->city_id          =$request->city_id;
        $user->is_provider      =$request->is_provider;

        if($request->active != "")
        {
            $user->active     =$request->active;
        }else
        {
            $user->active     =1;
        }

        $user->password   =bcrypt($request->password);

        if(!is_null($request->avatar))
        {
            $image = request('edit_photo');
            $name=date('d-m-y').time().rand()  .'.'.$image->getClientOriginalExtension();
            $image->move('public/dashboard/uploads/users/', $name);
            $user->avatar=$name;
        }




        $user->save();
        Report(Auth::user()->id,'بأضافة عضو جديد' . ' '.$user->name);
        Session::flash('success','تم اضافة العضو');
        return back();
    }

    #update user
    public function UpdateUser(Request $request)
    {
        $this->validate($request,[

            'edit_name'         =>'required|min:2|max:190',
            'edit_email'        =>'nullable|email|min:2|max:190|
                                    unique:users,email,'   . $request->id,
            'edit_phone'        => 'nullable|digits:10|unique:users,phone,'. $request->id,
            'edit_photo'        =>'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'edit_password'     =>'nullable|min:6|max:190',
            'edit_country_id'   =>'required|exists:countries,id',
            'edit_city_id'      =>'required|exists:cities,id',
        ],[
            'edit_name.required'=>'الاسم مطلوب',
            'email.email'=>'الايميل  يجب ان يكون من نوع ايميل',
            'edit_phone.required'=>'رقم الجوال مطلوب',
            'edit_phone.unique:users'=>'رقم الجوال مستخدم من قبل',
            'edit_country_id.required'=>'الدولة مطلوبة',
            'edit_city_id.required'=>'المدينة مطلوبة',
            'edit_photo.image'=>'الصورة يجب ان تكون من نوع صورة',
        ]);

        $user=User::findOrFail($request->id);

        $user->name       = $request->edit_name;

        if(!is_null($request->edit_email))
        {
            $user->email =$request->edit_email;
        }

        $user->phone =$request->edit_phone;

        if(!is_null($request->password))
        {
            $user->password =bcrypt($request->password);
        }

        if(!is_null($request->edit_country_id))
        {
            $user->country_id = $request->edit_country_id;
        }

        if(!is_null($request->edit_city_id))
        {
            $user->city_id = $request->edit_city_id;
        }

        if(!is_null($request->edit_is_provider))
        {
            $user->is_provider = $request->edit_is_provider;
        }

        if(!is_null($request->role))
        {
            if($user->id != 1)
            {
                $user->role =$request->role;
            }else
            {
                Session::flash('danger','لا يمكن تغير صلاحية هذا العضو');
            }
        }


        if($request->active != "")
        {
            if($user->id != 1)
            {
                $user->active     =$request->active;
            }else
            {
                Session::flash('danger','لا يمكن حظر هذا العضو');
            }
        }else
        {
            $user->active     =1;
        }

        if(!empty($request->edit_photo))
        {
            if($user->avatar != 'default.png')
            {
                File::delete('public/dashboard/uploads/users/'.$user->avatar);

                
                $image = request('edit_photo');
                $name=date('d-m-y').time().rand()  .'.'.$image->getClientOriginalExtension();
                $image->move('public/dashboard/uploads/users/', $name);


                $user->avatar=$name;

            }else{

                $image = request('edit_photo');
                $name=date('d-m-y').time().rand()  .'.'.$image->getClientOriginalExtension();
                $image->move('public/dashboard/uploads/users/', $name);

                $user->avatar=$name;
            }
        }

        $user->save();

        Report(Auth::user()->id,'بتحديث بيانات العضو '.$user->name);
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #delete user
    public function DeleteUser(Request $request)
    {
        if($request->id == 1)
        {
            Session::flash('danger','لا يمكن حذف هذا العضو');
            return back();
        }else
        {
            $user = User::findOrFail($request->id);
            File::delete('public/dashboard/uploads/users/'.$user->avatar);
            $user->delete();
            Report(Auth::user()->id,'بحذف العضو '.$user->name);
            Session::flash('success','تم الحذف');
            return back();
        }
    }


    # show order for User
    public function orderUser(Request $request){

        $user = User::findOrFail(request('id'));
        if($user->is_provider == 1){
            $orders = Order::where('finish',1)->where('delivery_id', $user->id)->get();
        }else{
            $orders = Order::where('user_id', $user->id)->get();
        }

        return view('dashboard.users.orders',compact('user','orders'));

    }


    # get country cities
    public function getCities(Country $country)
    {
        return $country->cities()->select('id', 'name_ar')->get();
    }

    #email correspondent for all users
//    public function EmailMessageAll(Request $request)
//    {
//        $this->validate($request,[
//            'email_message' =>'required|min:1'
//        ]);
//
//        $checkConfig = SmsEmailNotification::first();
//        if(
//            $checkConfig->smtp_type         == "" ||
//            $checkConfig->smtp_username     == "" ||
//            $checkConfig->smtp_password     == "" ||
//            $checkConfig->smtp_sender_email == "" ||
//            $checkConfig->smtp_port         == "" ||
//            $checkConfig->smtp_host         == ""
//        ){
//            Session::flash('danger','لم يتم ارسال الرساله ! .. يرجى مراجعة بيانات ال SMTP');
//            return back();
//        }else
//        {
//            $users = User::get();
//            foreach ($users as $u)
//            {
//                Mail::to($u->email)->send(new PublicMessage(  $request->email_message  ));
//            }
//            Session::flash('success','تم ارسال الرساله');
//            return back();
//        }
//    }

    #sms correspondent for all users
//    public function SmsMessageAll(Request $request)
//    {
//        $this->validate($request,[
//            'sms_message' =>'required'
//        ]);
//
//        $users = User::get();
//        foreach ($users as $u)
//        {
//            send_mobile_sms($u->phone,$request->sms_message);
//        }
//
//        Session::flash('success','تم ارسال الرساله');
//        return back();
//    }

    #notification correspondent for all users
//    public function NotificationMessageAll(Request $request)
//    {
//        $this->validate($request,[
//            'notification_message' =>'required'
//        ]);
//
//        $users = User::get();
//        foreach ($users as $u)
//        {
//            #use FCM or One Signal Here :)
//        }
//    }

    #end email for current user
//    public function SendEmail(Request $request)
//    {
//        $this->validate($request,[
//            'email_message' =>'required|min:1'
//        ]);
//
//        $checkConfig = SmsEmailNotification::first();
//        if(
//            $checkConfig->smtp_type         == "" ||
//            $checkConfig->smtp_username     == "" ||
//            $checkConfig->smtp_password     == "" ||
//            $checkConfig->smtp_sender_email == "" ||
//            $checkConfig->smtp_port         == "" ||
//            $checkConfig->smtp_host         == ""
//        ){
//            Session::flash('danger','لم يتم ارسال الرساله ! .. يرجى مراجعة بيانات ال SMTP');
//            return back();
//        }else
//        {
//            Mail::to($request->email)->send(new PublicMessage($request->email_message));
//            Session::flash('success','تم ارسال الرساله');
//            return back();
//        }
//    }

    #send sms for current user
//    public function SendSMS(Request $request)
//    {
//        $this->validate($request,[
//            'sms_message' =>'required'
//        ]);
//
//        send_mobile_sms($request->phone,$request->sms_message);
//        Session::flash('success','تم ارسال الرساله');
//        return back();
//    }

    #send notification for current user
//    public function SendNotification (Request $request)
//    {
//        $this->validate($request,[
//            'notification_message' =>'required'
//        ]);
//
//        #use FCM or One Signal Here :)
//    }
}
