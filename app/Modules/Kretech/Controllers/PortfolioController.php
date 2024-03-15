<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;
use Response;

class PortfolioController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Kretech Portfolio';
        $id = Auth::user()->id;

        // get user from table users
        $user = User::where('id', $id)->first();
            
        // verify user from table profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $user->email)->first();
        if ($profile == null) {
          Alert::error('Failed', 'Profile Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
        }
        
        // declare variable
        $api_url = env('API_URL');
        $code = $profile->cod;
        
        // get data portfolio from api
        $get_portfolio = Http::get($api_url . 'profile/' . $code)->json()['portfolio'];
        $get_portfolio = new Collection($get_portfolio);

        // check response
        if ($get_portfolio == '[]') {
          Alert::error('Failed', 'Profile Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
        }

        if ($request->ajax()) {
            $portfolio = $get_portfolio;
            $total_records = count($portfolio);

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $total_records,
                'recordsFiltered' => $total_records,
                'data' => $portfolio
            ]);

            // return Datatables::of($portfolio)->setTotalRecords($total_records)->setFilteredRecords(0)->make(true);
        }

        return view('Kretech::kretech_portfolio', [
            'title' => $title
        ]);
    }
}
