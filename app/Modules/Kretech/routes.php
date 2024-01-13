<?php

Route::group(['prefix' => 'kretech'], function () {

    Route::group(['namespace' => "Modules\Kretech\Controllers"], function () {

        // Kretech Dashboard
        Route::get('dashboard', 'DashboardController@index')->name('kretech.dashboard');

        // Kretech Profile
        Route::get('profile', 'ProfileController@index')->name('kretech.profile');

        // Kretech Portfolio
        Route::get('portfolio', 'PortfolioController@index')->name('kretech.portfolio');

        // Kretech Article
        Route::get('article', 'ArticleController@index')->name('kretech.article');
    });
});
