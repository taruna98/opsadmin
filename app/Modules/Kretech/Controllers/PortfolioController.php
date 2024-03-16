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
use Yajra\DataTables\DataTables;

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

        // check response
        if ($get_portfolio == '[]') {
          Alert::error('Failed', 'Profile Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
          return redirect()->back();
        }

        // show data if ajax request
        if ($request->ajax()) {
            return response()->json($get_portfolio);
        }

        return view('Kretech::kretech_portfolio', [
            'title' => $title
        ]);
    }

    public function store(Request $request)
    {
        return $request;

        $request->validate([
            'create_name'           => 'required|max:50',
            'create_email'          => 'required|email',
            'create_password'       => 'required',
            'create_img_profile'    => 'required|image|mimes:jpg|max:2048'
        ]);

        // get params
        $user_id    = Auth::user()->id;
        $module     = 'Admin';
        $scene      = 'User';
        $activity   = 'Create - ' . $request->create_email;
        $ip         = $request->ip();
        $api_url    = $_ENV['API_URL'];
        // ---
        $name       = $request->create_name;
        $email      = $request->create_email;
        $code       = generateCode();
        $password   = Hash::make($request->create_password);
        $role       = $request->create_role;
        $image      = $request->file('create_img_profile');
        $image_name = isset($image) ? 'admin_img_profile_' . strstr($email, '@', true) . '.' . $image->extension() : '';
        $status     = $request->create_status;

        // // temp variable
        // $temp = [
        //     'user_id'   => $user_id,
        //     'module'    => $module,
        //     'scene'     => $scene,
        //     'activity'  => $activity,
        //     'ip'        => $ip,
        //     // ---
        //     'name'      => $name,
        //     'email'     => $email,
        //     'code'      => $code,
        //     'password'  => $password,
        //     'role'      => $role,
        //     'image'     => $image_name,
        //     'status'    => $status
        // ];

        // check email user
        $user_check = User::where('email', $email)->first();
        if ($user_check) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // create user
        $user_create = User::create([
            'name'  => $name,
            'email' => $email,
            'password' => $password,
            'is_active' => $status,
        ]);
        $user_create->assignRole($role);

        if (!$user_create) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // check image
        if ($image !== null || $image != '') {
            $image->move(public_path('assets/img'), $image_name);
        }

        // check email profile
        $profile_check = Profile::where('eml', $email)->first();
        if ($profile_check) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // create profile
        $profile_create = Profile::create([
            'cod'   => $code,
            'eml'   => $email,
            'nme'   => $name,
            'stt'   => $status,
        ]);

        if (!$profile_create) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // store json file
        $response_store_json = Http::post($api_url . 'profile/store/' . $code);

        if (!$response_store_json) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
    }
}
