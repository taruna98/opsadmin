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
    
    // Activity Log Route
    Route::get('/activity-log', 'ActivityLogController@index')->name('activity_log');
});