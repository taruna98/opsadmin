<?php

namespace Modules\Kretech\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Alert;
use Auth;
use Response;
use Cache;

class PortfolioController extends BaseController
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index()
    {
        $title = 'Kretech Portfolio';

        return view('Kretech::kretech_portfolio', [
            'title'         => $title,
            // 'data_games'    => $data_games
        ]);
    }
}
