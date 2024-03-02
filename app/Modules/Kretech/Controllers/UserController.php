<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;


class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title = 'Kretech Dashboard';
        $email = Auth::user()->email;
        $user_id = Auth::user()->id;
        $role = Auth::user()->roles->pluck('name')[0];

        if ($role == 'owner' || $role == 'admin') {
            // get user kretech
            $role = 'kretech member';
            $user_kretech = User::whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            })->get();

            // get kretech activity
            $kretech_activity = DB::connection('mysql')->table('log_activity')->join('users', 'log_activity.user_id', '=', 'users.id')->select('users.name', 'log_activity.*')->where('log_activity.module', 'Admin')->orderBy('log_activity.created_at', 'desc')->limit(6)->get();

            return view('Kretech::kretech_dashboard', [
                'title'             => $title,
                'user_kretech'      => $user_kretech,
                'kretech_activity'  => $kretech_activity
            ]);
        } else {
            // get user
            $get_user = DB::connection('mysql')->table('users')->where('email', $email)->first();
    
            // get profile
            $get_profile = DB::connection('mysql2')->table('profiles')->where('eml', $get_user->email)->first();
    
            $get_profile->nme = $get_user->name;
            $get_profile->stt = $get_user->is_active;
    
            // get code
            $code = $get_profile->cod;
            $url = 'http://localhost:8000/profile/' . $code;
    
            // get json
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);
    
            // get activity
            $activity = DB::connection('mysql')->table('log_activity')->join('users', 'log_activity.user_id', '=', 'users.id')->select('users.name', 'log_activity.*')->where('log_activity.module', 'Admin')->where('log_activity.user_id', '1')->orderBy('log_activity.created_at', 'desc')->limit(2)->get();
    
            return view('Kretech::kretech_dashboard', [
                'title'     => $title,
                'data'      => $data,
                'activity'  => $activity
            ]);
        }
    }
}
