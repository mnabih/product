<?php

/*---------------------------------Start Of FrontEnd--------------------------*/
Route::get('/',function(){
    return view('site.index') ;
});



/*---------------------------------End Of FrontEnd--------------------------*/



/*---------------------------------Start Of DashBoard--------------------------*/

Route::group(['prefix'=>'admin','middleware'=>['auth','Manager','checkRole','smtpAndFcmConfig']],function(){
	/*Start Of DashBoard Controller (Intro Page)*/
	Route::get('dashboard',[
		'uses'  =>'DashBoardController@Index',
		'as'    =>'dashboard',
		'icon'  =>'<i class="icon-home4"></i>',
		'title' =>'الرئيسيه'
		]);


	/*------------ Start Of ContactUsController ----------*/

	#messages page
	Route::get('inbox-page',[
		'uses' =>'ContactUsController@InboxPage',
		'as'   =>'inbox',
		'title'=>'الرسائل',
		'icon' =>'<i class="icon-inbox-alt"></i>',
		'child' =>['showmessage','deletemessage','sendsms','sendemail']
	]);

	#show message page
	Route::get('show-message/{id}',[
		'uses'=>'ContactUsController@ShowMessage',
		'as'  =>'showmessage',
		'title'=>'عرض الرساله'
	]);

	#send sms
	Route::post('send-sms',[
		'uses' =>'ContactUsController@SMS',
		'as'   =>'sendsms',
		'title'=>'ارسال SMS'
	]);

	#send email
	Route::post('send-email',[
		'uses' =>'ContactUsController@EMAIL',
		'as'   =>'sendemail',
		'title'=>'ارسال Email'
	]);

	#delete message
	Route::post('delete-message',[
		'uses' =>'ContactUsController@DeleteMessage',
		'as'   =>'deletemessage',
		'title'=>'حذف الرساله'
	]);

	/*------------ End Of ContactUsController ----------*/

	/*------------ End Of UsersController ----------*/

	#users list
	Route::get('users',[
		'uses' =>'UsersController@Users',
		'as'   =>'users',
		'title'=>'الاعضاء', 
		'icon' =>'<i class="icon-vcard"></i>',
		'child'=>[
			'adduser',
			'updateuser',
			'deleteuser',
			'emailallusers',
			'smsallusers',
			'notificationallusers',
			'sendcurrentemail',
			'sendcurrentsms',
			'sendcurrentnotification'
		]
	]);

	#add user
	Route::post('add-user',[
		'uses' =>'UsersController@AddUser',
		'as'   =>'adduser',
		'title'=>'اضافة عضو'
	]);

	#update user
	Route::post('update-user',[
		'uses' =>'UsersController@UpdateUser',
		'as'   =>'updateuser',
		'title'=>'تحديث عضو'
	]);

	#delete user
	Route::post('delete-user',[
		'uses' =>'UsersController@deleteUser',
		'as'   =>'deleteuser',
		'title'=>'حذف عضو'
	]);

	#email for all users
	Route::post('email-users',[
		'uses' =>'UsersController@EmailMessageAll',
		'as'   =>'emailallusers',
		'title'=>'ارسال email للجميع'
	]);

	#sms for all users
	Route::post('sms-users',[
		'uses' =>'UsersController@SmsMessageAll',
		'as'   =>'smsallusers',
		'title'=>'ارسال sms للجميع'
	]);

	#notification for all users
	Route::post('notification-users',[
		'uses' =>'UsersController@NotificationMessageAll',
		'as'   =>'notificationallusers',
		'title'=>'ارسال notification للجميع'
	]);

	#send email for current user
	Route::post('send-current-email',[
		'uses' =>'UsersController@SendEmail',
		'as'   =>'sendcurrentemail',
		'title'=>'ارساله email لعضو'
	]);

	#send sms for current user
	Route::post('send-current-sms',[
		'uses' =>'UsersController@SendSMS',
		'as'   =>'sendcurrentsms',
		'title'=>'ارساله sms لعضو'
	]);

	#send notification for current user
	Route::post('send-current-notification',[
		'uses' =>'UsersController@SendNotification',
		'as'   =>'sendcurrentnotification',
		'title'=>'ارساله notification لعضو'
	]);
	/*------------ End Of UsersController ----------*/

	/*------------ Start Of ReportsController ----------*/

	#reports page
	Route::get('reports-page',[
		'uses' =>'ReportsController@ReportsPage',
		'as'   =>'reportspage',
		'title'=>'التقارير',
		'icon' =>'<i class=" icon-flag7"></i>',
		'child'=>['deleteusersreports','deletesupervisorsreports']
	]);

	#delete users reports
	Route::post('delete-users-reporst',[
		'uses' =>'ReportsController@DeleteUsersReports',
		'as'   =>'deleteusersreports',
		'title'=>'حذف تقارير الاعضاء'
	]);

	#delete supervisors reports
	Route::post('delete-supervisors-reporst',[
		'uses' =>'ReportsController@DeleteSupervisorsReports',
		'as'   =>'deletesupervisorsreports',
		'title'=>'حذف تقارير المشرفين'
	]);
	/*------------ End Of ReportsController ----------*/

	/*------------ start Of PermissionsController ----------*/
	#permissions list
	Route::get('permissions-list',[
		'uses' =>'PermissionsController@PermissionsList',
		'as'   =>'permissionslist',
		'title'=>'قائمة الصلاحيات',
		'icon' =>'<i class="icon-safe"></i>',
		'child'=>[
			'addpermissionspage',
			'addpermission',
			'editpermissionpage',
			'updatepermission',
			'deletepermission'
		]
	]);

	#add permissions page
	Route::get('permissions',[
		'uses' =>'PermissionsController@AddPermissionsPage', 
		'as'   =>'addpermissionspage',
		'title'=>'اضافة صلاحيه',

	]);

	#add permission
	Route::post('add-permission',[
		'uses' =>'PermissionsController@AddPermissions',
		'as'   =>'addpermission',
		'title' =>'تمكين اضافة صلاحيه'
	]);

	#edit permissions page
	Route::get('edit-permissions/{id}',[
		'uses' =>'PermissionsController@EditPermissions',
		'as'   =>'editpermissionpage',
		'title'=>'تعديل صلاحيه'
	]);

	#update permission
	Route::post('update-permission',[
		'uses' =>'PermissionsController@UpdatePermission',
		'as'   =>'updatepermission',
		'title'=>'تمكين تعديل صلاحيه'
	]);

	#delete permission
	Route::post('delete-permission',[
		'uses'=>'PermissionsController@DeletePermission',
		'as'  =>'deletepermission',
		'title' =>'حذف صلاحيه'
	]);

	/*------------ End Of PermissionsController ----------*/

	/*------------ Start Of MoneyAccountsController ----------*/
	Route::get('money-accounts',[
		'uses' =>'MoneyAccountsController@MoneyAccountsPage',
		'as'   =>'moneyaccountspage',
		'icon' =>'<i class="icon-cash3"></i>',
		'title'=>'الحسابات الماليه',
		'child'=>['moneyaccept','moneyacceptdelete','moneydelete']
	]);

	#accept
	Route::post('accept',[
		'uses' =>'MoneyAccountsController@Accept',
		'as'   =>'moneyaccept',
		'title'=>'تأكيد معامله بنكيه',
	]);

	#accept and delete
	Route::post('accept-delete',[
		'uses' =>'MoneyAccountsController@AcceptAndDelete',
		'as'   =>'moneyacceptdelete',
		'title'=>'تأكيد مع حذف',
	]);

	#delete
	Route::post('money-delete',[
		'uses' =>'MoneyAccountsController@Delete',
		'as'   =>'moneydelete',
		'title'=>'حذف معامله بنكيه',
	]);
	/*------------ End Of MoneyAccountsController ----------*/

	/*------------ Start Of SettingController ----------*/

	#setting page
	Route::get('setting',[
		'uses' =>'SettingController@Setting',
		'as'   =>'setting',
		'title'=>'الاعدادات',
		'icon' =>'<i class="icon-wrench"></i>',
		'child'=>[
			'addsocials',
			'updatesocials',
			'dddd',
			'updatesmtp',
			'updatesms',
			'updateonesignal',
			'updatefcm',
			'updatesitesetting',
			'updateseo',
			'updatesitecopyright',
			'updateemailtemplate',
			'updategoogleanalytics',
			'updatelivechat'
		]
	]);

	#add socials media
	Route::post('add-socials',[
		'uses' =>'SettingController@AddSocial',
		'as'   =>'addsocials',
		'title'=>'اضافة مواقع التواصل'
	]);

	#update socials media
	Route::post('update-socials',[
		'uses' =>'SettingController@UpdateSocial',
		'as'   =>'updatesocials',
		'title'=>'تحديث مواقع التواصل'
	]);

	#delete social
	Route::post('delete-social',[
		'uses' =>'SettingController@DeleteSocial',
		'as'   =>'deletesocial',
		'title'=>'حذف مواقع التاوصل'
	]);

	#update SMTP
	Route::post('update-smtp',[
		'uses' =>'SettingController@SMTP',
		'as'   =>'updatesmtp',
		'title'=>'تحديث SMTP'
	]);

	#update SMS
	Route::post('update-sms',[
		'uses' =>'SettingController@SMS',
		'as'   =>'updatesms',
		'title'=>'تحديث SMS'
	]);

	#update OneSignal
	Route::post('update-onesignal',[
		'uses' =>'SettingController@OneSignal',
		'as'   =>'updateonesignal',
		'title'=>'تحديث OneSignal'
	]);

	#update FCM
	Route::post('update-FCM',[
		'uses' =>'SettingController@FCM',
		'as'   =>'updatefcm',
		'title'=>'تحديث FCM'
	]);

	#update SiteSetting
	Route::post('update-sitesetting',[
		'uses' =>'SettingController@SiteSetting',
		'as'   =>'updatesitesetting',
		'title'=>'تحديث الاعدادات العامه'
	]);

	#update SEO
	Route::post('update-seo',[
		'uses' =>'SettingController@SEO',
		'as'   =>'updateseo',
		'title'=>'تحديث SEO'
	]);

	#update footerCopyRight
	Route::post('update-sitecopyright',[
		'uses' =>'SettingController@SiteCopyRight',
		'as'   =>'updatesitecopyright',
		'title'=>'تحديث حقوق الموقع'
	]);

	#update email template
	Route::post('update-emailtemplate',[
		'uses' =>'SettingController@EmailTemplate',
		'as'   =>'updateemailtemplate',
		'title'=>'تحديث قالب الايميل'
	]);

	#update api google analytics
	Route::post('update-google-analytics',[
		'uses' =>'SettingController@GoogleAnalytics',
		'as'   =>'updategoogleanalytics',
		'title'=>'تحديث google analytics'
	]);

	#update api live chat
	Route::post('update-live-chat',[
		'uses' =>'SettingController@LiveChat',
		'as'   =>'updatelivechat',
		'title'=>'تحديث live chat'
	]);

	/*------------ End Of SettingController ----------*/

	





});
	Route::get('dd',function(){
		 echo bcrypt(123456);
	});
/*-------------------------------End Of DashBoard--------------------------------*/



//Login Route
Route::get('/login/', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login/', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

// Route::get('register', 'Auth\RegisterController@showRegistrationForm');
// Route::post('register','RegisterUserController@Register');
//Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
