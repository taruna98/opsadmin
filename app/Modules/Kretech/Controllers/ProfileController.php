<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;
use Response;

class ProfileController extends BaseController
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
    $title = 'Kretech Profile';
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
    
    // get data profile from api
    $get_profile = Http::get($api_url . 'profile/' . $code)->json();
    
    // check response
    if ($get_profile == '[]') {
      Alert::error('Failed', 'Profile Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
      return redirect()->back();
    }

    return view('Kretech::kretech_profile', [
      'title'   => $title,
      'profile' => $get_profile
    ]);
  }
}
