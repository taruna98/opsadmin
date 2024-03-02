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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'kretech'], function () { // middleware web, auth

    Route::group(['middleware' => ['role_or_permission:owner|admin|kretech member'], 'namespace' => 'Modules\Kretech\Controllers'], function () {

        // Kretech Dashboard
        Route::get('dashboard', 'DashboardController@index')->name('kretech.dashboard');

        // Kretech User
        Route::get('user', 'UserController@index')->name('kretech.user');

        // Kretech Profile
        Route::get('profile', 'ProfileController@index')->name('kretech.profile');

        // Kretech Portfolio
        Route::get('portfolio', 'PortfolioController@index')->name('kretech.portfolio');

        // Kretech Article
        Route::get('article', 'ArticleController@index')->name('kretech.article');
        
        // Kretech Activity
        Route::get('activity', 'ActivityController@index')->name('kretech.activity');
    });
});
