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

Route::middleware(['auth:api'])->group(function ($router){
    $router->post('/profile/like', 'LikeController@likeProfile');
    $router->post('/profile/unlike', 'LikeController@unlikeProfile');
    $router->post('/profile/getisfollower', 'UserController@getisfollower');
    $router->post('/user/follow', 'UserController@follow');
    $router->post('/user/unfollow', 'UserController@unfollow');
    $router->post('/user/follow_request', 'UserController@followRequest');
    $router->post('/user/accept_follow_request', 'UserController@acceptFollowRequest');
    
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

    // $router->any('/logout', 'Api\AuthController@logout');
    $router->post('/portals/list', 'PortalController@list');
    $router->post('/users/list', 'UserController@list');
    
    $router->any('/profile/getallposts', 'UserController@getallpost');
    $router->post('/user/allfollows', 'UserController@allfollows');
    

    $router->post('/get/portal', 'Api\PortalController@getPortal');
    
    $router->post('/media/show/{id}', 'MediaController@show');
    
    $router->post('/portal/get_all_menus', 'Api\PortalController@get_all_menus');

    // **** Forum ****
    $router->post('/forum/index', 'Api\ForumController@index');
    $router->post('/forum/get_user_topics', 'Api\ForumController@getUserTopics');
    $router->post('/forum/detail', 'Api\ForumController@getDetail');
    
    $router->get('/strain/show/{id}', 'StrainController@show_mobile');
    $router->post('/marijuana-strains/follow', 'StrainController@follow');
    $router->put('/marijuana-strains/modal/{id}', 'StrainController@updateModal');
    $router->post('/marijuana-strains/get_all_follows', 'StrainController@getAllFollows');

    //Brand
    $router->get('/brand/get_all', 'BrandController@getBrands');
    $router->post('/get/brand', 'BrandController@appGetBrand');
    $router->post('/brand/get_all_menus', 'BrandController@get_all_menus');

    $router->post('/brand/get_category_media', 'BrandController@getCategoryMedia');
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
