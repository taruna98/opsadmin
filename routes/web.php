<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth Route
Route::get('/login', 'AuthController@index')->name('index');
Route::post('/login', 'AuthController@login')->name('login');

// Require Login
Route::middleware(['auth'])->group(function () {
    // Auth Route
    Route::get('/logout', 'AuthController@logout')->name('logout');

    // Home Route
    Route::get('/', 'HomeController@index')->name('home');

    // User Route
    Route::get('/user', 'UserController@index')->name('user');
    Route::post('/user/store', 'UserController@store')->name('user.store');
    Route::get('/user/edit/{id}', 'UserController@edit')->name('user.edit');
    Route::post('/user/update/{id}', 'UserController@update')->name('user.update');
    Route::get('/user/detail/{id}', 'UserController@detail')->name('user.detail');

    // Activity Log Route
    Route::get('/activity-log', 'ActivityLogController@index')->name('activity_log');
});

























<?php

Route::group(['middleware' => ['web', 'white', 'auth'], 'prefix' => 'fol'], function () { //middleware ip,auth , web

    Route::group(['middleware' => ['role_or_permission:Super Admin|Admin|Manager|Member|Ops Kiko Run'], 'namespace' => "Modules\FightOfLegends\Controllers"], function () {

        // dashboard
        Route::get('dashboard', 'DashboardController@index')->name("fol.dashboard");
        // end of dashboard
        
        // launcher event
        Route::get('launcher-event/index', 'LauncherEventController@index')->name('fol.launcher.event.index');
        Route::get('launcher-event/create', 'LauncherEventController@create')->name('fol.launcher.event.create');
        Route::post('launcher-event/store', 'LauncherEventController@store')->name('fol.launcher.event.store');
        Route::post('launcher-event/upload_image', 'LauncherEventController@upload_image')->name('fol.launcher.event.upload.image');
        Route::get('launcher-event/edit/{id}', 'LauncherEventController@edit')->name('fol.launcher.event.edit');
        Route::post('launcher-event/update/{id}', 'LauncherEventController@update')->name('fol.launcher.event.update');
        Route::get('launcher-event/detail/{id}', 'LauncherEventController@detail')->name('fol.launcher.event.detail');
        // end of launcher event

        // launcher news
        Route::get('launcher-news/index', 'LauncherNewsController@index')->name('fol.launcher.news.index');
        Route::get('launcher-news/create', 'LauncherNewsController@create')->name('fol.launcher.news.create');
        Route::post('launcher-news/store', 'LauncherNewsController@store')->name('fol.launcher.news.store');
        Route::post('launcher-news/upload_image', 'LauncherNewsController@upload_image')->name('fol.launcher.news.upload.image');
        Route::get('launcher-news/edit/{id}', 'LauncherNewsController@edit')->name('fol.launcher.news.edit');
        Route::post('launcher-news/update/{id}', 'LauncherNewsController@update')->name('fol.launcher.news.update');
        Route::get('launcher-news/detail/{id}', 'LauncherNewsController@detail')->name('fol.launcher.news.detail');
        // end of launcher news

        // web features
        Route::get('web-features/index', 'WebFeaturesController@index')->name('fol.web.features.index');
        Route::get('web-features/create', 'WebFeaturesController@create')->name('fol.web.features.create');
        Route::post('web-features/store', 'WebFeaturesController@store')->name('fol.web.features.store');
        Route::get('web-features/edit/{id}', 'WebFeaturesController@edit')->name('fol.web.features.edit');
        Route::post('web-features/update/{id}', 'WebFeaturesController@update')->name('fol.web.features.update');
        Route::get('web-features/detail/{id}', 'WebFeaturesController@detail')->name('fol.web.features.detail');
        // end of web features

        // web legend
        Route::get('web-legend/index', 'WebLegendController@index')->name('fol.web.legend.index');
        Route::get('web-legend/create', 'WebLegendController@create')->name('fol.web.legend.create');
        Route::post('web-legend/store', 'WebLegendController@store')->name('fol.web.legend.store');
        Route::get('web-legend/edit/{id}', 'WebLegendController@edit')->name('fol.web.legend.edit');
        Route::post('web-legend/update/{id}', 'WebLegendController@update')->name('fol.web.legend.update');
        Route::get('web-legend/detail/{id}', 'WebLegendController@detail')->name('fol.web.legend.detail');
        // end of web legend

        // web news
        Route::get('web-news/index', 'WebNewsController@index')->name('fol.web.news.index');
        Route::get('web-news/create', 'WebNewsController@create')->name('fol.web.news.create');
        Route::post('web-news/store', 'WebNewsController@store')->name('fol.web.news.store');
        Route::post('web-news/upload_image', 'WebNewsController@upload_image')->name('fol.web.news.upload.image');
        Route::get('web-news/edit/{id}', 'WebNewsController@edit')->name('fol.web.news.edit');
        Route::post('web-news/update/{id}', 'WebNewsController@update')->name('fol.web.news.update');
        Route::get('web-news/detail/{id}', 'WebNewsController@detail')->name('fol.web.news.detail');
        // end of web news
    });
});
