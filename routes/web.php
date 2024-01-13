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
Route::get('/login', 'AuthController@login')->name('login');

// Home Route
Route::get('/', 'HomeController@index')->name('home');

// User Route
Route::get('/user', 'UserController@index')->name('user');
