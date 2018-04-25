<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Image;
use App\Social;
use App\Html;
use App\SiteSetting;
use App\SmsEmailNotification;
use File;
use View;

class SettingController extends Controller
{
    public function __construct()
    {
        $socials     = Social::get();
        $SEN         = SmsEmailNotification::first();
        $SiteSetting = SiteSetting::first();
        $Html        = Html::first();
        View::share([
            'socials'     =>$socials,
            'SEN'         =>$SEN,
            'SiteSetting' =>$SiteSetting,
            'Html'        =>$Html
        ]);
    }

    #setting page
    public function Setting()
    {
    	return view('dashboard.setting.setting');
    }

    #add social media
    public function AddSocial(Request $request)
    {
        $this->validate($request,[
            'site_name' =>'required|min:1|max:190',
            'site_link' =>'required|min:5|max:190',
            'add_logo'  =>'required|image'
        ]);

        $social = new Social;
        $social->name  = $request->site_name;
        $social->link  = $request->site_link;

        $logo = $request->add_logo;
        $logo_name = date('d-m-y').time().rand().'.'.$logo->getClientOriginalExtension();
        $social->logo = $logo_name;
        if($social->save())
        {
            Image::make($logo)->save('dashboard/uploads/socialicon/'.$logo_name);
            Session::flash('success','تم الحفظ');
            return back();
        }
    }

    #update social 
    public function UpdateSocial(Request $request)
    {
        $this->validate($request,[
            'edit_site_name' =>'required|min:1|max:190',
            'edit_site_link' =>'required|min:5|max:190',
            'edit_logo' =>'nullable|image'
        ]);

        $social = Social::findOrFail($request->id);
        $social->name  = $request->edit_site_name;
        $social->link  = $request->edit_site_link;

        if(!empty($request->edit_logo))
        {
            $logo = $request->edit_logo;
            File::delete('dashboard/uploads/socialicon/'.$social->logo);
            if($social->save())
            {
                Image::make($logo)->save('dashboard/uploads/socialicon/'.$social->logo);
                Session::flash('success','تم حفظ التعديلات');
                return back();
            }
        }
        $social->save();
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #delete social
    public function DeleteSocial(Request $request)
    {
        $social = Social::findOrFail($request->id);
        File::delete('dashboard/uploads/socialicon/'.$social->logo);
        $social->delete();
        Session::flash('success','تم الحذف بنجاح');
        return back();
    }

    #update SMTP
    public function SMTP(Request $request)
    {
        $this->validate($request,[
            'smtp_type'        =>'nullable|min:1|max:190',
            'smtp_username'    =>'nullable|min:1|max:190',
            'smtp_sender_email'=>'nullable|min:1|max:190',
            'smtp_sender_name' =>'nullable|min:1|max:190',
            'smtp_password'    =>'nullable|min:1|max:190',
            'smtp_port'        =>'nullable|min:1|max:190',
            'smtp_host'        =>'nullable|min:1|max:190',
            'smtp_encryption'  =>'nullable|min:1|max:190',
        ]);

        $SEN = SmsEmailNotification::first();
        $SEN->smtp_type         = $request->smtp_type;
        $SEN->smtp_username     = $request->smtp_username;
        $SEN->smtp_sender_email = $request->smtp_sender_email;
        $SEN->smtp_sender_name  = $request->smtp_sender_name;
        $SEN->smtp_password     = $request->smtp_password;
        $SEN->smtp_port         = $request->smtp_port;
        $SEN->smtp_host         = $request->smtp_host;
        $SEN->smtp_encryption   = $request->smtp_encryption;
        $SEN->save();
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #update SMS
    public function SMS(Request $request)
    {
        $this->validate($request,[
            'sms_number'      =>'nullable|min:1|max:190',
            'sms_password'    =>'nullable|min:1|max:190',
            'sms_sender_name' =>'nullable|min:1|max:190'
        ]);

        $SEN = SmsEmailNotification::first();
        $SEN->sms_number      = $request->sms_number;
        $SEN->sms_password    = $request->sms_password;
        $SEN->sms_sender_name = $request->sms_sender_name;
        $SEN->save();
        Session::flash('success','تم حفظ التعديلات');
        return back(); 
    }

    #update onesignal
    public function OneSignal(Request $request)
    {
        $this->validate($request,[
            'oneSignal_application_id' =>'nullable|min:1|max:190',
            'oneSignal_authorization'  =>'nullable|min:1|max:190'
        ]);

        $SEN = SmsEmailNotification::first();
        $SEN->oneSignal_application_id = $request->oneSignal_application_id;
        $SEN->oneSignal_authorization  = $request->oneSignal_authorization;
        $SEN->save();
        Session::flash('success','تم حفظ التعديلات');
        return back(); 
    }

    #update FCM
    public function FCM(Request $request)
    {
        $this->validate($request,[
            'fcm_server_key' =>'nullable|min:1|max:190',
            'fcm_sender_id'  =>'nullable|min:1|max:190'
        ]);

        $SEN = SmsEmailNotification::first();
        $SEN->fcm_server_key = $request->fcm_server_key;
        $SEN->fcm_sender_id  = $request->fcm_sender_id;
        $SEN->save();
        Session::flash('success','تم حفظ التعديلات');
        return back(); 
    }

    #update SiteSetting
    public function SiteSetting(Request $request)
    {
        $this->validate($request,[
            'site_name' =>'nullable|min:1|max:190',
            'logo'      =>'nullable|image',
        ]);

        $SiteSetting = SiteSetting::first();
        $SiteSetting->site_name = $request->site_name;

        if(!empty($request->logo))
        {
            File::delete('dashboard/uploads/setting/site_logo/'.$SiteSetting->site_logo);
            $logo = $request->logo;
            $logo_name = 'logo.'.$logo->getclientoriginalextension();
            Image::make($logo)->save('dashboard/uploads/setting/site_logo/'.$logo_name);
            $SiteSetting->site_logo = $logo_name;
        }

        $SiteSetting->save();
        Session::flash('success','تم حفظ التعديلات');
        return back(); 
    }

    #update SEO
    public function SEO(Request $request)
    {
        $SiteSetting = SiteSetting::first();
        $SiteSetting->site_description = $request->site_description;
        $SiteSetting->site_tagged      = $request->site_tagged;
        $SiteSetting->site_copyrigth   = $request->site_copyrigth;
        $SiteSetting->save();
        Session::flash('success','تم حفظ التعديلات');
        return back(); 
    }

    #update siteCopyRight
    public function SiteCopyRight(Request $request)
    {
        $html = Html::first();
        $html->footer_copyrigh  = $request->footer_copyrigh;
        $html->save();
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #update email template
    public function EmailTemplate(Request $request)
    {
        $html = Html::first();
        $html->email_font_color   = $request->email_font_color;
        $html->email_header_color = $request->email_header_color;
        $html->email_footer_color = $request->email_footer_color;
        $html->save();
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #update api google analytics
    public function GoogleAnalytics(Request $request)
    {
        $html = Html::first();
        $html->google_analytics   = $request->google_analytics;
        $html->save();
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }

    #update api google live chat
    public function LiveChat(Request $request)
    {
        $html = Html::first();
        $html->live_chat   = $request->live_chat;
        $html->save();
        Session::flash('success','تم حفظ التعديلات');
        return back();
    }


}
