<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\PublicMessage;
use App\SmsEmailNotification;
use Mail;
use Auth;
use App\User;
use App\Role;
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
    	return view('dashboard.users.users',compact('users',$users,'roles',$roles));
    }

    #add user
    public function AddUser(Request $request)
    {
        $this->validate($request,[
            'name'     =>'required|min:2|max:190',
            'email'    =>'required|email|min:2|max:190|unique:users',
            'phone'    =>'required|min:2|max:190|unique:users',
            'avatar'   =>'required|image',
            'password' =>'required|min:6|max:190'
        ]);

		$user=new User;
		$user->name       =$request->name;
		$user->email      =$request->email;
        $user->phone      =$request->phone;
        $user->role       =$request->role;

        if($request->active != "")
        {
            $user->active     =$request->active;
        }else
        {
            $user->active     =1;
        }
		
		$user->password   =bcrypt($request->password);
        $photo=$request->avatar;
        $name=date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
        Image::make($photo)->save('dashboard/uploads/users/'.$name);
        $user->avatar=$name;
		$user->save();
        Report(Auth::user()->id,'بأضافة عضو جديد');
		Session::flash('success','تم اضافة العضو');
		return back();
    }

    #update user
    public function UpdateUser(Request $request)
    {
        $this->validate($request,[
            'edit_name'     =>'required|min:2|max:190',
            'edit_email'    =>'required|email|min:2|max:190',
            'edit_phone'    =>'required|min:2|max:190',
            'edit_photo'    =>'nullable|image',
            'password'      =>'nullable|min:6|max:190'
        ]);

		$user=User::findOrFail($request->id);
		$user->name       =$request->edit_name;

		if(User::where('email','=',$request->email)->count() == 0 || $user->email === $request->email)
        {
          $user->email =$request->edit_email;
        }

        if(User::where('phone','=',$request->phone)->count() == 0 || $user->phone === $request->phone)
        {
          $user->phone =$request->edit_phone;
        }


		if(!is_null($request->password))
		{
			$user->password =bcrypt($request->password);
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
            if($user->photo != 'default.png')
            {
                $photo=$request->edit_photo;
                File::delete('dashboard/uploads/users/'.$user->photo);
                Image::make($photo)->save('dashboard/uploads/users/'.$user->photo);
            }
            else
            {
                $photo=$request->edit_photo;
                $name=date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('dashboard/uploads/users/'.$name);
                $user->photo=$name;   
            }
        }

		$user->save();
        Report(Auth::user()->id,'بتحديث بيانات  '.$user->name);
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
            File::delete('dashboard/uploads/users/'.$user->photo);
            $user->delete();
            Report(Auth::user()->id,'بحذف العضو '.$user->name);
            Session::flash('success','تم الحذف');
            return back();
        }
    }

    #email correspondent for all users
    public function EmailMessageAll(Request $request)
    {
        $this->validate($request,[
            'email_message' =>'required|min:1'
        ]);

        $checkConfig = SmsEmailNotification::first();
        if(
            $checkConfig->smtp_type         == "" ||
            $checkConfig->smtp_username     == "" ||
            $checkConfig->smtp_password     == "" ||
            $checkConfig->smtp_sender_email == "" ||
            $checkConfig->smtp_port         == "" ||
            $checkConfig->smtp_host         == ""
        ){
            Session::flash('danger','لم يتم ارسال الرساله ! .. يرجى مراجعة بيانات ال SMTP');
            return back();
        }else
        {
            $users = User::get();
            foreach ($users as $u)
            {
                Mail::to($u->email)->send(new PublicMessage(  $request->email_message  ));
            }
            Session::flash('success','تم ارسال الرساله');
            return back();
        }
    }

    #sms correspondent for all users
    public function SmsMessageAll(Request $request)
    {
        $this->validate($request,[
            'sms_message' =>'required'
        ]);

        $users = User::get();
        foreach ($users as $u)
        {
            send_mobile_sms($u->phone,$request->sms_message);
        }
        
        Session::flash('success','تم ارسال الرساله');
        return back();
    }

    #notification correspondent for all users
    public function NotificationMessageAll(Request $request)
    {
        $this->validate($request,[
            'notification_message' =>'required'
        ]);

        $users = User::get();
        foreach ($users as $u)
        {
            #use FCM or One Signal Here :) 
        }
    }

    #end email for current user
    public function SendEmail(Request $request)
    {
        $this->validate($request,[
            'email_message' =>'required|min:1'
        ]);

        $checkConfig = SmsEmailNotification::first();
        if(
            $checkConfig->smtp_type         == "" ||
            $checkConfig->smtp_username     == "" ||
            $checkConfig->smtp_password     == "" ||
            $checkConfig->smtp_sender_email == "" ||
            $checkConfig->smtp_port         == "" ||
            $checkConfig->smtp_host         == ""
        ){
            Session::flash('danger','لم يتم ارسال الرساله ! .. يرجى مراجعة بيانات ال SMTP');
            return back();
        }else
        {
            Mail::to($request->email)->send(new PublicMessage($request->email_message));
            Session::flash('success','تم ارسال الرساله');
            return back();
        }
    }

    #send sms for current user
    public function SendSMS(Request $request)
    {
        $this->validate($request,[
            'sms_message' =>'required'
        ]);

        send_mobile_sms($request->phone,$request->sms_message);
        Session::flash('success','تم ارسال الرساله');
        return back();
    }

    #send notification for current user
    public function SendNotification (Request $request)
    {
        $this->validate($request,[
            'notification_message' =>'required'
        ]);

        #use FCM or One Signal Here :) 
    }
}
