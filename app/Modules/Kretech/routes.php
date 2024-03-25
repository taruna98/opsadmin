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
        Route::get('user/detail/{id}', 'UserController@detail')->name('kretech.user.detail');

        // Kretech Profile
        Route::get('profile', 'ProfileController@index')->name('kretech.profile');
        Route::post('profile/update', 'ProfileController@update')->name('kretech.profile.update');

        // Kretech Portfolio
        Route::get('portfolio', 'PortfolioController@index')->name('kretech.portfolio');
        Route::post('portfolio/store', 'PortfolioController@store')->name('kretech.portfolio.store');
        Route::get('portfolio/edit/{id}', 'PortfolioController@edit')->name('kretech.portfolio.edit');

        // Kretech Article
        Route::get('article', 'ArticleController@index')->name('kretech.article');
        
        // Kretech Activity
        Route::get('activity', 'ActivityController@index')->name('kretech.activity');
    });
});
