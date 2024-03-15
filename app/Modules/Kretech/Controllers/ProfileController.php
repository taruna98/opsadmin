<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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

    // check image profile update
    $check_image_profile = LogActivity::select('activity')->where('user_id', $id)->where('scene', 'Content/Profile')->where('activity', 'like', '% Image Profile')->orderBy('created_at', 'desc')->first();
    if ($check_image_profile) {
      $check_image_profile = explode('-', $check_image_profile->activity)[2];
      $check_image_profile = explode(' ', $check_image_profile)[1];
      $delete_image = ($check_image_profile == 'Delete') ? 1 : 0;
    } else {
      $delete_image = 0;
    }

    return view('Kretech::kretech_profile', [
      'title'         => $title,
      'profile'       => $get_profile,
      'delete_image'  => $delete_image
    ]);
  }

  public function store(Request $request)
  {
    // auth
    $auth = Auth::user();

    // request ajax delete image profile
    if ($request->ajax()) {

      // get profile
      $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();

      // get field
      $user_id    = $auth->id;
      $module     = 'Kretech';
      $scene      = 'Content/Profile';
      $activity   = 'Edit - ' . $auth->email . ' - Delete Image Profile';
      $ip         = $request->ip();
      // ---
      $code       = $profile->cod;
      $src_img    = asset('assets/img/img_profile_default.jpg');

      // delete image profile
      $path_image_default = public_path('assets/img/img_profile_default.jpg');
      $path_image_profile = public_path('assets/img/kretech_img_profile_' . $code . '.jpg');
  
      if (!File::copy($path_image_default, $path_image_profile)) {
        return response()->json(['message' => 'Avatar Anda gagal dihapus!'], 500);
      }

      // save log activity
      $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
      if (!$save_log_activity) {
          Alert::error('Failed', 'Update Profile')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
      }

      return response()->json(['message' => 'success', 'src' => $src_img], 200);
    }

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
        // save profile image
        $image->move(public_path('assets/img'), $image_name);
        
        // var log activity
        $activity   = 'Edit - ' . $auth->email . ' - Change Image Profile';
        
        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Update Profile')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }
      }

      // update name in table user
      $update_user = User::where('email', $auth->email)->update(['name' => strtolower($name)]);
      
      if (!$update_user) {
        Alert::error('Failed', 'Update User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }

      // get data profile from api
      $get_profile = Http::get($api_url . 'profile/' . $code)->json();
      
      // check response
      if ($get_profile == null) {
        Alert::error('Failed', 'Profile Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }

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
      $validator = Validator::make($request->all(), [
        'current_password'  => 'required',
        'new_password'      => 'required',
        'new_password_2'    => 'required'
      ]);
  
      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      if ($request->new_password != $request->new_password_2) {
        Alert::error('Failed', 'Wrong New Password')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }

      if (!Hash::check($request->current_password, $auth->password)) {
        Alert::error('Failed', 'Wrong Old Password')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }

      // get field
      $user_id    = $auth->id;
      $module     = 'Kretech';
      $scene      = 'Content/Password';
      $activity   = 'Edit - ' . $auth->email;
      $ip         = $request->ip();

      // change password
      $auth->password = Hash::make($request->new_password);
      $change_password = $auth->save();

      if (!$change_password) {
        Alert::error('Failed', 'Change Password')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }

      // save log activity
      $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
      if (!$save_log_activity) {
          Alert::error('Failed', 'Change Password')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
      }

      Alert::success('Success', 'Change Password')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
      return redirect()->back();
    }
  }
}
