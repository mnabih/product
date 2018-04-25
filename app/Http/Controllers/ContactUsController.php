<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailMessage;
use App\SmsEmailNotification;
use Session;

class ContactUsController extends Controller
{
    #inbox page
    public function InboxPage()
    {
    	$messages = Contact::latest()->get();
    	return view('dashboard.inbox.inbox',compact('messages',$messages));
    }

    #show message
    public function ShowMessage($id)
    {
    	$message = Contact::findOrFail($id);
    	$message->ShowOrNow = 1;
    	$message->update();
    	return view('dashboard.inbox.show_message',compact('message',$message));
    }

    #send SMS
    public function SMS(Request $request)
    {
        $this->validate($request,[
            'phone'       =>'required',
            'sms_message' =>'required|min:1'
        ]);

        if(send_mobile_sms($request->phone,$request->sms_message))
        {
            Session::flash('success','تم ارسال الرساله');
            return back();
        }else
        {
            Session::flash('warning','لم يتم ارسال الرساله ! ... تأكد من بيانات ال SMTP');
            return back();
        }
    }

    #send EMAIL
    public function EMAIL(Request $request)
    {
        $this->validate($request,[
            'email'=>'required',
            'email_message' =>'required|min:1'
        ]);

        #check if smtp congiration complete or no
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
            Mail::to($request->email)->send(new MailMessage($request->email_message));
            Session::flash('success','تم ارسال الرساله');
            return back();
        }
    }

    #delete mesage
    public function DeleteMessage(Request $request)
    {
    	Contact::findOrFail($request->id)->delete();
    	Session::flash('success','تم حذف الرساله');
    	return back();
    }
}
