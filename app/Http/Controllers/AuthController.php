<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends BaseController
{
    public function index()
    {
        $title = 'Login to Your Account';

        return view('auth/login', [
            'title' => $title,
        ]);
    }
 
    public function login(Request $request)
    {
        $apiUrl = 'http://localhost:8000/login';

        $response = Http::post($apiUrl, [
            'eml' => $request->input('email'),
            'pas' => $request->input('password'),
        ]);
        
        $data = $response->json();

        if ($data['suc']) {
            Session::put('api_token', $data['dat']['tok']);

            return redirect('/');

            // return view('admin_home', [
            //     'data'  => $data
            // ]);
        } else {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['login' => ['Login failed. Please check your credentials.']]);
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::forget('api_token');

        return redirect('/login');
    }
}
