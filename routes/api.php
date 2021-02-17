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
Route::get('/user/visited/{id}', 'UserController@visited');

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
Route::post('/brand/save', 'BrandController@save');
Route::get('/brand/get_modal_data', 'BrandController@getModalData');

Route::post('/marijuana-news', 'BlogController@index');
Route::get('/marijuana-news/{slug}', 'BlogController@show');


Route::post('/portals', 'PortalController@getAllPortals');
Route::get('/users', 'UserController@api');
Route::get('/marijuana-strains', 'StrainController@api');

// Sitemap Routes

Route::get('/sitemap', 'SitemapController@index');
Route::get('/sitemap/strains', 'SitemapController@strains');
Route::get('/sitemap/users', 'SitemapController@users');
Route::get('/sitemap/companies', 'SitemapController@companies');
Route::get('/sitemap/states', 'SitemapController@states');
Route::get('/sitemap/forums', 'SitemapController@forums');
Route::get('/sitemap/news', 'SitemapController@news');
Route::get('/sitemap/get_modal_data', 'SitemapController@getModalData');

// Forum

Route::any('/topic/all','ForumsController@all');
Route::any('/topic/detail/{forumid}','ForumsController@getdetail');
Route::any('/topic/setkeyword','ForumsController@setkeyword');
Route::any('/gettopicuser/{user_id}','ForumsController@gettopicuser');
Route::any('/topic/getedittopic','ForumsController@getedittopic');
Route::get('/marijuana-forums/{id}','ForumsController@detail');

Route::post('/portal/get_all_menus', 'PortalController@get_all_menus');

Route::post('/usermessages/imageupload/{sender}/{receiver}', 'UserchatController@imageupload');

// **** App Forum ****

Route::post('/forum/index', 'Api\ForumController@index');
Route::post('/forum/get_user_topics', 'Api\ForumController@getUserTopics');
Route::post('/forum/detail', 'Api\ForumController@getDetail');

// **** App Portal Routes ****
Route::post('/portals/add', 'Api\PortalController@store');
Route::post('/app/portal/get_all_menus', 'Api\PortalController@get_all_menus');
Route::post('/portals/list', 'PortalController@list');
Route::post('/app/get/portal', 'Api\PortalController@getPortal');

// Description
Route::post('/get_description', 'SettingController@getDescription');
Route::post('/update_description', 'SettingController@updateDescription');

Route::middleware(['auth:api'])->group(function ($router){
    $router->post('/profile/like', 'LikeController@likeProfile');
    $router->post('/profile/unlike', 'LikeController@unlikeProfile');
    $router->post('/profile/update', 'UserController@update');
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

    $router->post('/media/api_store', 'MediaController@api_store');
    $router->post('/media', 'MediaController@store')->middleware('cors.api');
    $router->post('/media/update', 'MediaController@api_update');    
    $router->get('/media/{media}', 'MediaController@destroy');   
    $router->put('/media/{media}', 'MediaController@update'); 
    $router->delete('/media/{media}', 'MediaController@destroy');

    $router->post('/users/list', 'UserController@list');  
    $router->delete('/user/{id}', 'UserController@destroy');
    $router->post('/user/activate', 'UserController@activate');      
    
    $router->post('/portals/update', 'PortalController@update');
    
    $router->post('/brand/update', 'BrandController@update');
    $router->get('/brand/delete/{id}', 'BrandController@delete');


    $router->post('/coupon/store', 'CouponController@store');
    $router->post('/coupon/{id}', 'CouponController@update');
    $router->delete('/coupon/{id}', 'CouponController@destroy');
    
    // **** Forum Desktop ***
    $router->any('/topic/create','ForumsController@create');
    $router->any('/topic/delete','ForumsController@delete');
    $router->any('/topic/edit','ForumsController@edit');
    $router->any('/topicreply/create','ForumsController@replycreate');
    $router->any('/topic/like','ForumsController@like');
    $router->any('/topic/bookmark','ForumsController@bookmark');

    // App Forum
    
    $router->post('/app/topic/like','Api\ForumController@like');
    $router->post('/app/topic/bookmark','Api\ForumController@bookmark');
    $router->post('/app/topic/create','Api\ForumController@create');
    $router->post('/app/topic/edit','Api\ForumController@edit');
    $router->post('/app/topic/reply','Api\ForumController@reply');
    $router->get('/app/topic/delete/{id}','Api\ForumController@delete');
    
    $router->get('/strain/show/{id}', 'StrainController@show_mobile');
    $router->post('/marijuana-strains/follow', 'StrainController@follow');
    $router->put('/marijuana-strains/modal/{id}', 'StrainController@updateModal');
    $router->put('/marijuana-brands/modal/{id}', 'BrandController@updateModal');
    $router->put('/sitemap/modal/{id}', 'SitemapController@updateModal');

    $router->post('/notification/get_all', 'NotificationController@get_all');
    $router->post('/email_notification_filter/save', 'NotificationFilterController@save');
    $router->any('/email_notification_filter/get_value', 'NotificationFilterController@get_value');
    $router->post('/notification/get_unreads', 'NotificationController@user_unreads');

    // Menu
    
    $router->post('/menu/create', 'PortalController@create_menu')->name('menu.create');
    $router->post('/menu/update', 'PortalController@update_menu')->name('menu.update');
    $router->get('/menu/{id}/delete', 'PortalController@delete_menu')->name('menu.delete');
    $router->get('/menu/{id}/deactive', 'PortalController@deactive_menu')->name('menu.deactive');

    // Messenger    
    $router->any('/user/chatlist', 'UserController@chatlist');
    $router->any('/user/getmessengerstatus', 'UserController@getMessengerStatus');
    $router->post('/user/searchuser', 'UserController@searchuser');
    $router->get('/user/togglemessenger', 'UserController@togglemessenger');
    $router->post('/user/deletemessage', 'UserController@deletemessage');
    
    $router->post('/user/block', 'UserController@block');
    $router->get('/user/blockuserlist', 'UserController@blockuserlist');
    $router->get('/user/blockuserlistonmobile', 'UserController@blockuserlistonmobile');
    $router->post('/user/enableblock', 'UserController@enableblock');
    $router->post('/user/isblockuser', 'UserController@isblockuser');
    $router->post('/user/get_unreads', 'UserController@getUnreads');

    $router->post('/usermessages/fetch', 'UserchatController@fetchMessages');
    $router->post('/usermessages/send', 'UserchatController@sendMessage');
    $router->post('/usermessages/readall', 'UserchatController@readall');

    // Admin Panel

    $router->any('/admin/post/all', 'AdminBlogController@index');
    $router->get('/admin/post/add', 'AdminBlogController@create');
    $router->post('/admin/post/add', 'AdminBlogController@store');
    $router->post('/admin/post/{id}/update', 'AdminBlogController@update');
    $router->get('/admin/post/{id}/delete', 'AdminBlogController@destroy');    

    Route::get('/admin/category', 'AdminCategoryController@index');
    Route::post('/admin/category/add', 'AdminCategoryController@store');
    Route::post('/admin/category/{id}/update', 'AdminCategoryController@update');
    Route::post('/admin/categoryremove/{id}', 'AdminCategoryController@delete');
    Route::get('/admin/category/order/{id}/up', 'AdminCategoryController@upOrder');
    Route::get('/admin/category/order/{id}/down', 'AdminCategoryController@downOrder');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');
    Route::any('/app/logout', 'Api\AuthController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');

    // Custom Mobile Router
    
    Route::post('/app/portals/update', 'Api\PortalController@update');
    Route::post('/app/profile/update', 'Api\UserController@update'); 
    
    Route::post('/app/menu/create', 'Api\PortalController@create_menu');
    Route::post('/app/menu/update', 'Api\PortalController@update_menu');

});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');

    
    Route::post('/portals/store', 'PortalController@store');

    
    Route::post('/password/email', 'Auth\PasswordController@validatePasswordRequest');
    Route::post('/password/reset', 'Auth\PasswordController@resetPassword');

    // Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    // Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('email/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');

    // Custom Auth    
    Route::post('/app/register', 'Api\AuthController@register');
    Route::post('/app/login', 'Api\AuthController@login');
});
