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


    /********************************** start newProject ************************************/

    Route::any('about-app'                  ,'SettingController@AboutApp');
    Route::any('appInfo'                    ,'SettingController@appInfo');
    Route::any('contact-us'                 ,'SettingController@socialLinks');
    Route::any('countries'                  ,'SettingController@countries');
    Route::any('countryCities'              ,'SettingController@countryCities');

    Route::any('provider-register'          ,'AuthController@provider_register');
    Route::any('customer-register'          ,'AuthController@customer_register');
    Route::any('provider_login'             ,'AuthController@provider_login');
    Route::any('customer_login'             ,'AuthController@customer_login');
    Route::any('providerForgetPassword'     ,'AuthController@providerForgetPassword');
    Route::any('providerUpdatePassword'     ,'AuthController@providerUpdatePassword');

    Route::any('providerEditProfile'        ,'AuthController@providerEditProfile');
    Route::any('customerEditProfile'        ,'AuthController@customerEditProfile');
    
    Route::any('logOut'                     ,'AuthController@logOut');
    Route::any('activeUser'                 ,'AuthController@activeUser');
    Route::any('userData'                 ,'AuthController@userData');

    /********** start newProject product ***********************/
    Route::any('allProducts'                ,'HomeController@allProducts');
    Route::any('alltypes'                   ,'HomeController@alltypes');
    Route::any('productsSortWithFilter'     ,'HomeController@productsSortWithFilter');
    Route::any('showProduct'                ,'HomeController@showProduct');
    

    Route::any('addToCart'                  ,'HomeController@addToCart');
    Route::any('deleteFromCart'             ,'HomeController@deleteFromCart');
    Route::any('showCart'                   ,'HomeController@showCart');
    Route::any('makeOrder'                  ,'HomeController@makeOrder');
    Route::any('reBuyOldOrder'              ,'HomeController@reBuyOldOrder');
    Route::any('myBills'                    ,'HomeController@myBills');
    Route::any('billdetail'                 ,'HomeController@billdetail');
    Route::any('deleteOrder'                ,'HomeController@deleteOrder');



    Route::any('unfinishedOrders'           ,'HomeController@unfinishedOrders');
    Route::any('orderBill'                  ,'HomeController@orderBill');
    Route::any('takeOrder'                  ,'HomeController@takeOrder');
    Route::any('myFinishOrder'              ,'HomeController@myFinishOrder');


    Route::any('showNotifications'          ,'HomeController@showNotifications');
    Route::any('deleteNotify'               ,'HomeController@deleteNotify');

    /********************************* end newProject ***************************************/


});
//** End SettingController**//

Route::group(['middleware' => ['mobile'] , 'namespace' => 'API'], function() {

         Route::any('edit-profile'            ,'AuthController@editProfile');
         Route::any('edit-password'           ,'AuthController@editPassword');

});