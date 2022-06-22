<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Index routes
 */
Route::get('/', ['as' => '/', 'uses' => 'AnalyticsController@index'])->middleware(['auth']);
Route::post('/', 'AnalyticsController@index')->middleware('role:admin');



/**
 * Fixinglist routes
 */
Route::get('fixinglist',  ['as' => 'fixinglist', 'uses' => 'AnalyticsController@home'])->middleware('auth');
Route::group(array('prefix' => 'fixinglist', 'middleware' => 'auth'), function()
{
    Route::get('categories',  ['as' => 'fixinglist.categories', 'uses' => 'AnalyticsController@categories'])->middleware('permission:fixinglist.categories.main');
    Route::get('categories/{query}',  ['as' => 'fixinglist.categories.query','uses' => 'AnalyticsController@categories']);
    Route::get('filter',  ['as' => 'fixinglist.filter', 'uses' => 'AnalyticsController@filter']);

    Route::get('users',  ['as' => 'fixinglist.users', 'uses' => 'AnalyticsController@users'])->middleware('permission:fixinglist.users');
    Route::get('clicks',  ['as' => 'fixinglist.clicks', 'uses' => 'AnalyticsController@clicks'])->middleware('permission:fixinglist.clicks');

    Route::get('bounces',  ['as' => 'fixinglist.bounces', 'uses' => 'AnalyticsController@bounces'])->middleware('permission:fixinglist.bounces');

    Route::get('channels',  ['as' => 'fixinglist.channels', 'uses' => 'AnalyticsController@channels'])->middleware('permission:fixinglist.channels.main');
    Route::get('channels/{query}',  ['as' => 'fixinglist.channels.query','uses' => 'AnalyticsController@channels']);
    
    Route::get('blog',  ['as' => 'fixinglist.blog', 'uses' => 'AnalyticsController@blog'])->middleware('permission:fixinglist.blog.main');
    Route::get('blog/filter',  ['as' => 'fixinglist.blog.filter', 'uses' => 'AnalyticsController@blogFilter']);
    Route::get('blog/{query}',  ['as' => 'fixinglist.blog.query','uses' => 'AnalyticsController@blog']);

    Route::get('catalog',  ['as' => 'fixinglist.catalog', 'uses' => 'AnalyticsController@catalog'])->middleware('permission:fixinglist.catalog.main');
    Route::get('catalog/filter',  ['as' => 'fixinglist.catalog.filter', 'uses' => 'AnalyticsController@catalogFilter']);
    Route::get('catalog/{query}',  ['as' => 'fixinglist.catalog.query','uses' => 'AnalyticsController@catalog']);

    Route::get('master',  ['as' => 'fixinglist.master', 'uses' => 'AnalyticsController@catalog'])->middleware('permission:fixinglist.master.main');
    Route::get('master/filter',  ['as' => 'fixinglist.master.filter', 'uses' => 'AnalyticsController@catalogFilter']);
    Route::get('master/{query}',  ['as' => 'fixinglist.master.query','uses' => 'AnalyticsController@catalog']);

	Route::get('requests',  ['as' => 'fixinglist.requests', 'uses' => 'RequestController@index'])->middleware('permission:fixinglist.catalog.main');
	Route::get('requests/api',  ['as' => 'fixinglist.requests.api', 'uses' => 'RequestController@api']);
	Route::get('requests/{query}',  ['as' => 'fixinglist.requests.query','uses' => 'RequestController@index']);

    Route::get('mobile',  ['as' => 'fixinglist.mobile','uses' => 'AnalyticsController@mobile'])->middleware('permission:fixinglist.mobile');
    Route::get('mobile/{query}',  ['as' => 'fixinglist.mobile.query','uses' => 'AnalyticsController@mobile'])->middleware('permission:fixinglist.mobile');
    
    Route::get('mobile-download',  ['as' => 'fixinglist.mobile-download','uses' => 'FirebaseController@index'])->middleware('permission:fixinglist.mobile-download');
    Route::get('mobile-download-custom',  ['as' => 'fixinglist.mobile-download-custom','uses' => 'FirebaseController@indexCustom'])->middleware('permission:fixinglist.mobile-download');
    Route::post('mobile-download-custom',  ['as' => 'fixinglist.mobile-download-custom','uses' => 'FirebaseController@indexCustom'])->middleware('permission:fixinglist.mobile-download');



    Route::get('adwords',  ['as' => 'fixinglist.adwords','uses' => 'AnalyticsController@adwords'])->middleware('permission:fixinglist.adwords.main');
});

/**
 * Ustabor routes
 */
Route::get('ustabor', ['as' => 'ustabor', 'uses' => 'AnalyticsController@home'])->middleware('auth');
Route::group(array('prefix' => 'ustabor', 'middleware' => 'auth'), function()
{
        Route::get('categories',  ['as' => 'ustabor.categories', 'uses' => 'AnalyticsController@categories'])->middleware('permission:ustabor.categories.main');
        Route::get('categories/{query}',  ['as' => 'ustabor.categories.query','uses' => 'AnalyticsController@categories']);
        Route::get('filter',  ['as' => 'ustabor.filter', 'uses' => 'AnalyticsController@filter']);

        Route::get('users',  ['as' => 'ustabor.users', 'uses' => 'AnalyticsController@users'])->middleware('permission:ustabor.users');
        Route::get('clicks',  ['as' => 'ustabor.clicks', 'uses' => 'AnalyticsController@clicks'])->middleware('permission:ustabor.clicks');
        Route::get('bounces',  ['as' => 'ustabor.bounces', 'uses' => 'AnalyticsController@bounces'])->middleware('permission:ustabor.bounces');

        Route::get('channels',  ['as' => 'ustabor.channels', 'uses' => 'AnalyticsController@channels'])->middleware('permission:ustabor.channels.main');
        Route::get('channels/{query}',  ['as' => 'ustabor.channels.query','uses' => 'AnalyticsController@channels']);

    Route::get('blog',  ['as' => 'ustabor.blog', 'uses' => 'AnalyticsController@blog'])->middleware('permission:ustabor.blog.main');
    Route::get('blog/filter',  ['as' => 'ustabor.blog.filter', 'uses' => 'AnalyticsController@blogFilter']);
    Route::get('blog/{query}',  ['as' => 'ustabor.blog.query','uses' => 'AnalyticsController@blog']);

    Route::get('catalog',  ['as' => 'ustabor.catalog', 'uses' => 'AnalyticsController@catalog'])->middleware('permission:ustabor.catalog.main');
    Route::get('catalog/filter',  ['as' => 'ustabor.catalog.filter', 'uses' => 'AnalyticsController@catalogFilter']);
    Route::get('catalog/{query}',  ['as' => 'ustabor.catalog.query','uses' => 'AnalyticsController@catalog']);

    Route::get('master',  ['as' => 'ustabor.master', 'uses' => 'AnalyticsController@catalog'])->middleware('permission:ustabor.master.main');
    Route::get('master/filter',  ['as' => 'ustabor.master.filter', 'uses' => 'AnalyticsController@catalogFilter']);
    Route::get('master/{query}',  ['as' => 'ustabor.master.query','uses' => 'AnalyticsController@catalog']);

	Route::get('requests',  ['as' => 'ustabor.requests', 'uses' => 'RequestController@index'])->middleware('permission:ustabor.catalog.main');
	Route::get('requests/api',  ['as' => 'ustabor.requests.api', 'uses' => 'RequestController@api']);
	Route::get('requests/{query}',  ['as' => 'ustabor.requests.query','uses' => 'RequestController@index']);

    Route::get('mobile',  ['as' => 'ustabor.mobile','uses' => 'AnalyticsController@mobile'])->middleware('permission:ustabor.mobile');
    Route::get('mobile/{query}',  ['as' => 'ustabor.mobile.query','uses' => 'AnalyticsController@mobile'])->middleware('permission:ustabor.mobile');


    Route::get('mobile-download',  ['as' => 'ustabor.mobile-download','uses' => 'FirebaseController@index'])->middleware('permission:ustabor.mobile-download');
    Route::get('mobile-download-custom',  ['as' => 'ustabor.mobile-download-custom','uses' => 'FirebaseController@indexCustom'])->middleware('permission:ustabor.mobile-download');
    Route::post('mobile-download-custom',  ['as' => 'ustabor.mobile-download-custom','uses' => 'FirebaseController@indexCustom'])->middleware('permission:ustabor.mobile-download');



    Route::get('adwords',  ['as' => 'ustabor.adwords','uses' => 'AnalyticsController@adwords'])->middleware('permission:ustabor.adwords.main');
});


/**
 * Url list routes
 */
Route::resource('url-list', 'UrlListController')->middleware('auth');



/**
 * User management routes
 */
Route::resource('users', 'Auth\UsersController', [
        'names' => [
            'index' => 'users',
            'destroy' => 'user.destroy'
        ]
    ])->middleware('role:admin');

/**
 * RBAC management routes
 */
Route::group(array('prefix' => 'rbac', 'middleware' => 'auth'), function()
{

    Route::resource('roles', 'Auth\Rbac\RolesController', [
        'names' => [
            'index' => 'roles',
            'destroy' => 'roles.destroy'
        ]
    ])->middleware('role:admin');

//    if (App::isLocal()):
    Route::resource('permissions', 'Auth\Rbac\PermissionsController', [
        'names' => [
            'index' => 'permissions',
            'destroy' => 'permissions.destroy'
        ]
    ])->middleware('role:admin');
//    endif;

    Route::resource('permission-groups', 'Auth\Rbac\PermissionGroupsController', [
        'names' => [
            'index' => 'permission-groups',
            'destroy' => 'permission-groups.destroy'
        ]
    ])->middleware('role:admin');

});

Route::group(['middleware' => ['web']], function() {
    Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
    Route::post('login', ['as' => 'login.post', 'uses' => 'Auth\LoginController@login']);
    Route::post('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);
    Route::post('password.request', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);
});
