<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/post/allhomepost', 'MediaController@allpost');
Route::post('/comment/getall', 'CommentController@getcomment');
Route::get('/get_strain/{strain}', 'StrainController@show');
Route::get('/marijuana-strains', 'StrainController@api');
Route::post('/strain/get_menus', 'StrainController@getMenus');
Route::post('/strain-media/{slug}', 'StrainController@getMedia');
Route::get('/strain/get_modal_data', 'StrainController@getModalData');
Route::resource('/marijuana-strains', 'StrainController');
Route::post('/marijuana-strains/get_all_follows', 'StrainController@getAllFollows');

Route::get('/get_profile/{slug}', 'UserController@index');
Route::post('/profile/getfollow', 'UserController@getfollow');
Route::post('/user/allfollows', 'UserController@allfollows');
Route::any('/profile/getallposts', 'UserController@getallpost');
Route::post('/user/isblockuser', 'UserController@isblockuser');
Route::post('/portal/get_distance', 'PortalController@getDistance');

Route::post('/categories', 'CategoryController@get_all_categories');
Route::post('/category/strains', 'CategoryController@get_strains');
Route::post('/category-strains', 'CategoryController@getCategoryStrains');
Route::put('/category/{id}', 'CategoryController@update');


Route::post('/media/upload', 'MediaController@mediauplaod')->middleware('cors.api');
Route::post('/media/show/{id}', 'MediaController@show');
Route::post('/comment/count_comments', 'CommentController@count_comments');

Route::post('/medical-recreational-marijuana-dispensary-delivery', 'PortalController@searchmap');
Route::post('/portals/getall', 'PortalController@searchPortals');

Route::post('/marijuana-coupons', 'CouponController@show');

//Brand
Route::get('/brand/get_all', 'BrandController@getBrands');
Route::post('/get/brand', 'BrandController@appGetBrand');
Route::post('/brand/get_all_menus', 'BrandController@get_all_menus');
Route::post('/brand/get_category_media', 'BrandController@getCategoryMedia');

Route::post('/marijuana-news', 'BlogController@index');
Route::get('/marijuana-news/{slug}', 'BlogController@show');

Route::middleware(['auth:api'])->group(function ($router){
    $router->post('/profile/like', 'LikeController@likeProfile');
    $router->post('/profile/unlike', 'LikeController@unlikeProfile');
    $router->post('/profile/getisfollower', 'UserController@getisfollower');
    $router->post('/user/follow', 'UserController@follow');
    $router->post('/user/unfollow', 'UserController@unfollow');
    $router->post('/user/follow_request', 'UserController@followRequest');
    $router->post('/user/accept_follow_request', 'UserController@acceptFollowRequest');
    $router->post('/profile/getisfollower', 'UserController@getisfollower');
    
    $router->post('/marijuana-strains/like', 'StrainController@like');
    
    $router->post('/comment/add', 'CommentController@addcomment');
    $router->post('/comment/update', 'CommentController@updatecomment');
    $router->post('/comment/addlike', 'LikeController@addcommentlike');
    $router->post('/comment/unlike', 'LikeController@deletecommentlike');
    $router->post('/comment/deletecomment', 'CommentController@destroy');
    
    $router->post('/like/addlike', 'LikeController@create');
    $router->post('/like/unlike', 'LikeController@delete');
    $router->post('/bookmark/store', 'SaveController@store');
    $router->post('/bookmark/delete', 'SaveController@destroy');
    $router->post('/media', 'MediaController@api_store');
    $router->post('/media/update', 'MediaController@api_update');    
    $router->get('/media/{media}', 'MediaController@destroy');

    $router->post('/portals/list', 'PortalController@list');
    $router->post('/users/list', 'UserController@list');    

    $router->post('/get/portal', 'Api\PortalController@getPortal');    
    
    $router->post('/portal/get_all_menus', 'Api\PortalController@get_all_menus');


    $router->post('/coupon/store', 'CouponController@store');
    $router->post('/coupon/{id}', 'CouponController@update');
    $router->delete('/coupon/{id}', 'CouponController@destroy');
    // **** Forum ****
    $router->post('/forum/index', 'Api\ForumController@index');
    $router->post('/forum/get_user_topics', 'Api\ForumController@getUserTopics');
    $router->post('/forum/detail', 'Api\ForumController@getDetail');
    
    $router->get('/strain/show/{id}', 'StrainController@show_mobile');
    $router->post('/marijuana-strains/follow', 'StrainController@follow');
    $router->put('/marijuana-strains/modal/{id}', 'StrainController@updateModal');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');

});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('email/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});
