<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
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

  public function store(Request $request)
  {
    if ($request->updatefor == 'profile') {
      $validator = Validator::make($request->all(), [
        'name'          => 'required',
        'about'         => 'required',
        'profession'    => 'required',
        // 'profile_image' => 'required|image|mimes:jpg|max:2048',
        'tools'         => 'required',
        'skill'         => 'required'
      ]);
  
      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      // auth
      $auth = Auth::user();

      // get profile
      $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();

      // get field
      $user_id    = $auth->id;
      $module     = 'Kretech';
      $scene      = 'Content/Profile';
      $activity   = 'Edit - ' . $auth->email;
      $ip         = $request->ip();
      $api_url    = env('API_URL');
      // ---
      $email      = $profile->eml;
      $code       = $profile->cod;
      $name       = $request->name;
      $about      = $request->about;
      $profession = $request->profession;
      $tools      = $request->tools;
      $skill      = $request->skill;
      $image      = $request->file('profile_image');
      $image_name = isset($image) ? 'kretech_img_profile_' . $code . '.' . $image->extension() : '';

      // // temp variable
      // $temp = $email . ' ~ ' . $code . ' ~ ' . $name . ' ~ ' . $about . ' ~ ' . $profession . ' ~ ' . $tools . ' ~ ' . $skill;

      // check profile image
      if ($image !== null || $image != '') {
        $image->move(public_path('assets/img'), $image_name);
      }

      // update name in table user
      $update_user = User::where('email', $auth->email)->update(['name' => strtolower($name)]);
      
      if (!$update_user) {
        Alert::error('Failed', 'Update User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }



      // $2y$10$UoNMt0lJJUo4MaC9VX9wnegWqDVIrVbeEIJVmpbn3Rsc34NEDbrb6


      // get data profile from api
      $get_profile = Http::get($api_url . 'profile/' . $code)->json();
      
      // check response
      if ($get_profile == null) {
        Alert::error('Failed', 'Profile Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }
      
      // setup json
      $get_profile['profile']['nme'] = $name;
      $get_profile['profile']['mds'] = $about;
      $get_profile['profile']['hsb'] = $profession;
      $get_profile['profile']['mtl'] = $tools;
      $get_profile['profile']['msk'] = $skill;

      // update json
      $update_profile_json = Http::post($api_url . 'profile/update/' . $code, [
        'name'        => $name,
        'about'       => $about,
        'profession'  => $profession,
        'tools'       => $tools,
        'skill'       => $skill
      ]);

      if ($update_profile_json != 'success update file') {
        Alert::error('Failed', 'Update Profile')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }

      // save log activity
      $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
      if (!$save_log_activity) {
          Alert::error('Failed', 'Update Profile')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
      }

      Alert::success('Success', 'Update Profile')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
      return redirect()->back();
    } else if ($request->updatefor == 'password') {
      // 
    }
  }
}
