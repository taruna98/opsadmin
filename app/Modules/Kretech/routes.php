<?php

Route::group(['prefix' => 'kretech'], function () {

    Route::group(['namespace' => "Modules\Kretech\Controllers" ], function () {

        Route::get('dashboard', 'DashboardController@index')->name('kretech.dashboard');
        // Route::get('/dashboard', function () {
        //     return view('Kretech::kretech_dashboard');  
        // })->name('kretech.dashboard');

    });

});