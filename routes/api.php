<?php

header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


	
//** Start SettingController**//
Route::group( ['namespace' => 'API'], function() {
    Route::any('phone-keys'         ,'SettingController@phoneKeys');
    Route::any('sign-up'            ,'AuthController@signUp');
    Route::any('sign-in'            ,'AuthController@signIn');
    Route::any('forget-password'    ,'AuthController@forgetPassword');
    Route::any('update-password'    ,'AuthController@updatePassword');
});
//** End SettingController**//

Route::group(['middleware' => ['mobile'] , 'namespace' => 'API'], function() {

         Route::any('edit-profile'            ,'AuthController@editProfile');
         Route::any('edit-password'           ,'AuthController@editPassword');

});