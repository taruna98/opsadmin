<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    public function login()
    {
        $title = 'Login';

        return view('auth/login', [
            'title' => $title,
        ]);
    }
}
