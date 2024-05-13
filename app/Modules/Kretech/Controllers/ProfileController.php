<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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

    // get cv
    // Storage::url('public/file/pdf/' . $code . '_CV.pdf')
    $cv_url = (File::exists(public_path('file/pdf/' . $code . '_CV.pdf'))) ? $code : '0';

    // check image profile update
    $check_image_profile = LogActivity::select('activity')->where('user_id', $id)->where('scene', 'Content/Profile/Profile')->where('activity', 'like', '% Image Profile')->orderBy('created_at', 'desc')->first();
    if ($check_image_profile) {
      $check_image_profile = explode('-', $check_image_profile->activity)[2];
      $check_image_profile = explode(' ', $check_image_profile)[1];
      $delete_image_profile = ($check_image_profile == 'Delete') ? 1 : 0;
    } else {
      $delete_image_profile = 0;
    }

    // check background home update
    $check_background_home = LogActivity::select('activity')->where('user_id', $id)->where('scene', 'Content/Profile/Background')->where('activity', 'like', '% Background Home')->orderBy('created_at', 'desc')->first();
    if ($check_background_home) {
      $check_background_home = explode('-', $check_background_home->activity)[2];
      $check_background_home = explode(' ', $check_background_home)[1];
      $delete_background_home = ($check_background_home == 'Delete') ? 1 : 0;
    } else {
      $delete_background_home = 0;
    }

    // check background service update
    $check_background_service = LogActivity::select('activity')->where('user_id', $id)->where('scene', 'Content/Profile/Background')->where('activity', 'like', '% Background Service')->orderBy('created_at', 'desc')->first();
    if ($check_background_service) {
      $check_background_service = explode('-', $check_background_service->activity)[2];
      $check_background_service = explode(' ', $check_background_service)[1];
      $delete_background_service = ($check_background_service == 'Delete') ? 1 : 0;
    } else {
      $delete_background_service = 0;
    }

    // check background article update
    $check_background_article = LogActivity::select('activity')->where('user_id', $id)->where('scene', 'Content/Profile/Background')->where('activity', 'like', '% Background Article')->orderBy('created_at', 'desc')->first();
    if ($check_background_article) {
      $check_background_article = explode('-', $check_background_article->activity)[2];
      $check_background_article = explode(' ', $check_background_article)[1];
      $delete_background_article = ($check_background_article == 'Delete') ? 1 : 0;
    } else {
      $delete_background_article = 0;
    }

    // check profile cv update
    $check_profile_cv = LogActivity::select('activity')->where('user_id', $id)->where('scene', 'Content/Profile/CV')->where('activity', 'like', '% Profile CV')->orderBy('created_at', 'desc')->first();
    if ($check_profile_cv) {
      $check_profile_cv = explode('-', $check_profile_cv->activity)[2];
      $check_profile_cv = explode(' ', $check_profile_cv)[1];
      $delete_profile_cv = ($check_profile_cv == 'Delete') ? 1 : 0;
      $upload_profile_cv = ($check_profile_cv == 'Upload') ? 1 : 0;
    } else {
      $delete_profile_cv = 0;
      $upload_profile_cv = 0;
    }

    return view('Kretech::kretech_profile', [
      'title'                     => $title,
      'profile'                   => $get_profile,
      'cv_url'                    => $cv_url,
      'delete_image_profile'      => $delete_image_profile,
      'delete_background_home'    => $delete_background_home,
      'delete_background_service' => $delete_background_service,
      'delete_background_article' => $delete_background_article,
      'delete_profile_cv'         => $delete_profile_cv,
      'upload_profile_cv'         => $upload_profile_cv
    ]);
  }

  public function update(Request $request)
  {
    // auth
    $auth = Auth::user();

    // request ajax delete image
    if ($request->ajax()) {

      // get profile
      $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();

      // get field
      $user_id                    = $auth->id;
      $module                     = 'Kretech';
      $ip                         = $request->ip();
      $destination_url            = env('API_URL') . 'data/upload_image.php';
      $destination_url_2          = env('API_URL') . 'data/upload_file.php';
      // ---
      $code                       = $profile->cod;
      $src_profile_img_def        = asset('assets/img/img_profile_default.jpg');
      $src_background_home_def    = asset('assets/img/kretech_img_profile_bg_home_default.jpg');
      $src_background_service_def = asset('assets/img/kretech_img_profile_bg_service_default.jpg');
      $src_background_article_def = asset('assets/img/kretech_img_profile_bg_article_default.jpg');

      if ($request->input('action') == 'delete_profile_image') {
        $scene    = 'Content/Profile/Profile';
        $activity = 'Edit - ' . $auth->email . ' - Delete Image Profile';

        /** CURL profile image */
        $curl = curl_init();
        // Set destination URL
        curl_setopt($curl, CURLOPT_URL, $destination_url);
        curl_setopt($curl, CURLOPT_POST, true);
        $profile_default = public_path('assets\img\img_profile_default.jpg');
        $profile_upload = new UploadedFile(
            $profile_default,
            'img_profile_default.jpg',
            mime_content_type($profile_default),
            filesize($profile_default),
            false
        );
        $profile_name_upload = isset($profile_upload) ? $code . '-img-profile' . '.' . $profile_upload->extension() : '';
        $profile_upload_path = $profile_upload->path();
        $data = array(
            'profile_file_1' => new \CURLFile($profile_upload_path, $profile_upload->getClientMimeType(), $profile_name_upload)
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        /** CURL photo break */
        if ($result === false) {
            Alert::error('Failed', 'Set Profile Image Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

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

        return response()->json(['message' => 'success', 'src' => $src_profile_img_def], 200);
      } else if ($request->input('action') == 'delete_background_home') {
        $scene    = 'Content/Profile/Background';
        $activity = 'Edit - ' . $auth->email . ' - Delete Background Home';

        /** CURL background home */
        $curl = curl_init();
        // Set destination URL
        curl_setopt($curl, CURLOPT_URL, $destination_url);
        curl_setopt($curl, CURLOPT_POST, true);
        $background_home_default = public_path('assets\img\kretech_img_profile_bg_home_default.jpg');
        $background_home_upload = new UploadedFile(
            $background_home_default,
            'kretech_img_profile_bg_home_default.jpg',
            mime_content_type($background_home_default),
            filesize($background_home_default),
            false
        );
        $background_home_name_upload = isset($background_home_upload) ? $code . '-bg-home' . '.' . $background_home_upload->extension() : '';
        $background_home_upload_path = $background_home_upload->path();
        $data = array(
            'background_home_file_1' => new \CURLFile($background_home_upload_path, $background_home_upload->getClientMimeType(), $background_home_name_upload)
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        /** CURL photo break */
        if ($result === false) {
            Alert::error('Failed', 'Set Background Home Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // delete background home
        $path_image_default = public_path('assets/img/kretech_img_profile_bg_home_default.jpg');
        $path_image_profile = public_path('assets/img/kretech_img_profile_bg_home_' . $code . '.jpg');
    
        if (!File::copy($path_image_default, $path_image_profile)) {
          return response()->json(['message' => 'Background Home Anda gagal dihapus!'], 500);
        }

        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Update Background Home')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        return response()->json(['message' => 'success', 'src' => $src_background_home_def], 200);
      } else if ($request->input('action') == 'delete_background_service') {
        $scene    = 'Content/Profile/Background';
        $activity = 'Edit - ' . $auth->email . ' - Delete Background Service';

        /** CURL background service */
        $curl = curl_init();
        // Set destination URL
        curl_setopt($curl, CURLOPT_URL, $destination_url);
        curl_setopt($curl, CURLOPT_POST, true);
        $background_service_default = public_path('assets\img\kretech_img_profile_bg_service_default.jpg');
        $background_service_upload = new UploadedFile(
            $background_service_default,
            'kretech_img_profile_bg_service_default.jpg',
            mime_content_type($background_service_default),
            filesize($background_service_default),
            false
        );
        $background_service_name_upload = isset($background_service_upload) ? $code . '-bg-service' . '.' . $background_service_upload->extension() : '';
        $background_service_upload_path = $background_service_upload->path();
        $data = array(
            'background_service_file_1' => new \CURLFile($background_service_upload_path, $background_service_upload->getClientMimeType(), $background_service_name_upload)
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        /** CURL photo break */
        if ($result === false) {
            Alert::error('Failed', 'Set Background Service Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // delete background service
        $path_image_default = public_path('assets/img/kretech_img_profile_bg_service_default.jpg');
        $path_image_profile = public_path('assets/img/kretech_img_profile_bg_service_' . $code . '.jpg');
    
        if (!File::copy($path_image_default, $path_image_profile)) {
          return response()->json(['message' => 'Background Service Anda gagal dihapus!'], 500);
        }

        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Update Background Service')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        return response()->json(['message' => 'success', 'src' => $src_background_service_def], 200);
      } else if ($request->input('action') == 'delete_background_article') {
        $scene    = 'Content/Profile/Background';
        $activity = 'Edit - ' . $auth->email . ' - Delete Background Article';

        /** CURL background article */
        $curl = curl_init();
        // Set destination URL
        curl_setopt($curl, CURLOPT_URL, $destination_url);
        curl_setopt($curl, CURLOPT_POST, true);
        $background_article_default = public_path('assets\img\kretech_img_profile_bg_article_default.jpg');
        $background_article_upload = new UploadedFile(
            $background_article_default,
            'kretech_img_profile_bg_article_default.jpg',
            mime_content_type($background_article_default),
            filesize($background_article_default),
            false
        );
        $background_article_name_upload = isset($background_article_upload) ? $code . '-bg-article' . '.' . $background_article_upload->extension() : '';
        $background_article_upload_path = $background_article_upload->path();
        $data = array(
            'background_article_file_1' => new \CURLFile($background_article_upload_path, $background_article_upload->getClientMimeType(), $background_article_name_upload)
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        /** CURL photo break */
        if ($result === false) {
            Alert::error('Failed', 'Set Background Article Default')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // delete background article
        $path_image_default = public_path('assets/img/kretech_img_profile_bg_article_default.jpg');
        $path_image_profile = public_path('assets/img/kretech_img_profile_bg_article_' . $code . '.jpg');
    
        if (!File::copy($path_image_default, $path_image_profile)) {
          return response()->json(['message' => 'Background Article Anda gagal dihapus!'], 500);
        }

        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Update Background Article')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        return response()->json(['message' => 'success', 'src' => $src_background_article_def], 200);
      } else if ($request->input('action') == 'delete_profile_cv') {
        $scene    = 'Content/Profile/CV';
        $activity = 'Edit - ' . $auth->email . ' - Delete Profile CV';

        if (!File::exists(public_path('file/pdf/' . $code . '_CV.pdf'))) {
          return response()->json(['message' => 'not found'], 404);
        }
        
        // delete profile cv
        $delete_cv = File::delete(public_path('file/pdf/' . $code . '_CV.pdf'));

        if (!$delete_cv) {
          return response()->json(['message' => 'not modified'], 304);
        }

        /** CURL Curiculum Vitae */
        $curl = curl_init();
        // Set destination URL
        curl_setopt($curl, CURLOPT_URL, $destination_url_2);
        curl_setopt($curl, CURLOPT_POST, true);
        $data = array(
          'profile_cv_file_1' => 'delete cv|' . $code . '_CV.pdf'
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        /** CURL file break */
        if ($result === false) {
          return response()->json(['message' => 'not modified'], 304);
        }
  
        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Delete Profile CV')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        return response()->json(['message' => 'success'], 200);
      }
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
      $user_id            = $auth->id;
      $module             = 'Kretech';
      $scene              = 'Content/Profile/Profile';
      $activity           = 'Edit - ' . $auth->email;
      $ip                 = $request->ip();
      $api_url            = env('API_URL');
      $destination_url    = env('API_URL') . 'data/upload_image.php';
      // ---
      $email              = $profile->eml;
      $code               = $profile->cod;
      $name               = $request->name;
      $about              = $request->about;
      $profession         = $request->profession;
      $tools              = $request->tools;
      $skill              = $request->skill;
      $profile_image      = $request->file('profile_image');
      $profile_image_name = isset($profile_image) ? 'kretech_img_profile_' . $code . '.' . $profile_image->extension() : '';
      
      // temp variable
      $temp = [
        'user_id'       => $user_id,
        'module'        => $module,
        'scene'         => $scene,
        'activity'      => $activity,
        'ip'            => $ip,
        // ---
        'email'         => $email,
        'code'          => $code,
        'name'          => $name,
        'about'         => $about,
        'profession'    => $profession,
        'tools'         => $tools,
        'skill'         => $skill,
        'profile_image' => $profile_image_name
      ];

      // return $temp;

      // check profile image
      if ($profile_image !== null || $profile_image != '') {
        /** CURL profile image */
        $curl = curl_init();
        // Set destination URL
        curl_setopt($curl, CURLOPT_URL, $destination_url);
        curl_setopt($curl, CURLOPT_POST, true);
        $profile_image_upload = $request->file('profile_image');
        $profile_image_name_upload = isset($profile_image_upload) ? $code . '-img-profile.' . $profile_image_upload->extension() : '';
        $profile_image_upload_path = $profile_image_upload->path();
        $data = array(
          'profile_file_1' => new \CURLFile($profile_image_upload_path, $profile_image_upload->getClientMimeType(), $profile_image_name_upload)
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        /** CURL photo break */
        if ($result === false) {
          Alert::error('Failed', 'Set Profile Image')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
        }

        // save profile image
        $profile_image->move(public_path('assets/img'), $profile_image_name);
        
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
      $scene      = 'Content/Profile/Password';
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
    } else if ($request->updatefor == 'background') {
      $validator = Validator::make($request->all(), [
        // 'background_home' => 'required|image|mimes:jpg|max:2048',
      ]);
  
      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      // get profile
      $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();

      // get field
      $user_id                  = $auth->id;
      $module                   = 'Kretech';
      $scene                    = 'Content/Profile/Background';
      $ip                       = $request->ip();
      $api_url                  = env('API_URL');
      $destination_url          = env('API_URL') . 'data/upload_image.php';
      // ---
      $email                    = $profile->eml;
      $code                     = $profile->cod;
      $background_home          = $request->file('background_home');
      $background_home_name     = isset($background_home) ? 'kretech_img_profile_bg_home_' . $code . '.' . $background_home->extension() : '';
      $background_service       = $request->file('background_service');
      $background_service_name  = isset($background_service) ? 'kretech_img_profile_bg_service_' . $code . '.' . $background_service->extension() : '';
      $background_article       = $request->file('background_article');
      $background_article_name  = isset($background_article) ? 'kretech_img_profile_bg_article_' . $code . '.' . $background_article->extension() : '';
      
      // temp variable
      $temp = [
        'user_id'             => $user_id,
        'module'              => $module,
        'scene'               => $scene,
        'ip'                  => $ip,
        'api_url'             => $api_url,
        'destination_url'     => $destination_url,
        // ---
        'email'               => $email,
        'code'                => $code,
        'background_home'     => $background_home_name,
        'background_service'  => $background_service_name,
        'background_article'  => $background_article_name
      ];

      // check background home
      if ($background_home !== null || $background_home != '') {
        /** CURL background home */
        $curl = curl_init();
        // Set destination URL
        curl_setopt($curl, CURLOPT_URL, $destination_url);
        curl_setopt($curl, CURLOPT_POST, true);
        $background_home_upload = $request->file('background_home');
        $background_home_name_upload = isset($background_home_upload) ? $code . '-bg-home.' . $background_home_upload->extension() : '';
        $background_home_upload_path = $background_home_upload->path();
        $data = array(
          'background_home_file_1' => new \CURLFile($background_home_upload_path, $background_home_upload->getClientMimeType(), $background_home_name_upload)
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        /** CURL photo break */
        if ($result === false) {
          Alert::error('Failed', 'Set Background Home')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
        }

        // save background home
        $background_home->move(public_path('assets/img'), $background_home_name);
        
        // var log activity
        $activity   = 'Edit - ' . $auth->email . ' - Change Background Home';
        
        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Update Background Home')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Update Background Home')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
      } else if ($background_service !== null || $background_service != '') {
        /** CURL background service */
        $curl = curl_init();
        // Set destination URL
        curl_setopt($curl, CURLOPT_URL, $destination_url);
        curl_setopt($curl, CURLOPT_POST, true);
        $background_service_upload = $request->file('background_service');
        $background_service_name_upload = isset($background_service_upload) ? $code . '-bg-service.' . $background_service_upload->extension() : '';
        $background_service_upload_path = $background_service_upload->path();
        $data = array(
          'background_service_file_1' => new \CURLFile($background_service_upload_path, $background_service_upload->getClientMimeType(), $background_service_name_upload)
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        /** CURL photo break */
        if ($result === false) {
          Alert::error('Failed', 'Set Background Service')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
        }

        // save background service
        $background_service->move(public_path('assets/img'), $background_service_name);
        
        // var log activity
        $activity   = 'Edit - ' . $auth->email . ' - Change Background Service';
        
        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Update Background Service')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Update Background Service')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
      } else if ($background_article !== null || $background_article != '') {
        /** CURL background article */
        $curl = curl_init();
        // Set destination URL
        curl_setopt($curl, CURLOPT_URL, $destination_url);
        curl_setopt($curl, CURLOPT_POST, true);
        $background_article_upload = $request->file('background_article');
        $background_article_name_upload = isset($background_article_upload) ? $code . '-bg-article.' . $background_article_upload->extension() : '';
        $background_article_upload_path = $background_article_upload->path();
        $data = array(
          'background_article_file_1' => new \CURLFile($background_article_upload_path, $background_article_upload->getClientMimeType(), $background_article_name_upload)
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        /** CURL photo break */
        if ($result === false) {
          Alert::error('Failed', 'Set Background Article')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
        }

        // save background article
        $background_article->move(public_path('assets/img'), $background_article_name);
        
        // var log activity
        $activity   = 'Edit - ' . $auth->email . ' - Change Background Article';
        
        // save log activity
        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Update Background Article')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Update Background Article')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
      }
    } else if ($request->updatefor == 'cv') {
      $validator = Validator::make($request->all(), [
        'profile_cv' => 'required|mimes:pdf|max:2048'
      ]);

      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      // get profile
      $profile = DB::connection('mysql2')->table('profiles')->where('eml', $auth->email)->first();

      // get field
      $user_id          = $auth->id;
      $module           = 'Kretech';
      $scene            = 'Content/Profile/CV';
      $activity         = 'Edit - ' . $auth->email . ' - Upload Profile CV';
      $ip               = $request->ip();
      $destination_url  = env('API_URL') . 'data/upload_file.php';
      // ---
      $file_name        = $profile->cod . '_CV.pdf';

      /** CURL Curiculum Vitae */
      $curl = curl_init();
      // Set destination URL
      curl_setopt($curl, CURLOPT_URL, $destination_url);
      curl_setopt($curl, CURLOPT_POST, true);
      $profile_cv_upload = $request->file('profile_cv');
      $profile_cv_name_upload = isset($profile_cv_upload) ? $profile->cod . '_CV.' . $profile_cv_upload->extension() : '';
      $profile_cv_upload_path = $profile_cv_upload->path();
      $data = array(
        'profile_cv_file_1' => new \CURLFile($profile_cv_upload_path, $profile_cv_upload->getClientMimeType(), $profile_cv_name_upload)
      );
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      $result = curl_exec($curl);
      /** CURL file break */
      if ($result === false) {
        Alert::error('Failed', 'Set Curiculum Vitae')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }

      // upload profile cv to public
      $save_pdf = $request->file('profile_cv')->move(public_path('file/pdf'), $file_name);

      if (!$save_pdf) {
        Alert::error('Failed', 'Upload Profile CV')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
        return redirect()->back();
      }

      // save log activity
      $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
      if (!$save_log_activity) {
          Alert::error('Failed', 'Upload Profile CV')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
      }

      Alert::success('Success', 'Upload Profile CV')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
      return redirect()->back();
    }
  }
}
