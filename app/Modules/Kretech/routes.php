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
        Route::post('portfolio/update/{id}', 'PortfolioController@update')->name('kretech.portfolio.update');
        Route::get('portfolio/detail/{id}', 'PortfolioController@detail')->name('kretech.portfolio.detail');
        Route::post('portfolio/file', 'PortfolioController@file')->name('kretech.portfolio.file');
        
        // Kretech Article
        Route::get('article', 'ArticleController@index')->name('kretech.article');
        Route::post('article/store', 'ArticleController@store')->name('kretech.article.store');
        Route::get('article/edit/{id}', 'ArticleController@edit')->name('kretech.article.edit');
        Route::post('article/update/{id}', 'ArticleController@update')->name('kretech.article.update');
        Route::get('article/detail/{id}', 'ArticleController@detail')->name('kretech.article.detail');
        Route::post('article/upload_image', 'ArticleController@upload_image')->name('kretech.article.upload.image');
        
        // Kretech Activity
        Route::get('activity', 'ActivityController@index')->name('kretech.activity');
    });
});
