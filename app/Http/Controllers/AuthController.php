<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;


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
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect('/');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['login' => ['Login failed. Please check your credentials.']]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
