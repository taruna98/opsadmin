<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class HomeController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index()
    {
        // return Auth::user();
        $title = 'Home';
        // $data_games = Games::where('status',1)->get();

        return view('admin_home', [
            'title'         => $title,
            // 'data_games'    => $data_games
        ]);
    }
}
