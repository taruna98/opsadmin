<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


class HomeController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $title = 'Home';

        // get user kretech
        $role = 'kretech member';
        $user_kretech = User::whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->get();

        // get all activity
        $all_activity = DB::connection('mysql')->table('log_activity')->join('users', 'log_activity.user_id', '=', 'users.id')->select('users.name', 'log_activity.*')->orderBy('log_activity.created_at', 'desc')->limit(6)->get();

        return view('admin_home', [
            'title'         => $title,
            'user_kretech'  => $user_kretech,
            'all_activity'  => $all_activity
        ]);


        // return Auth::user()->roles->pluck('name')[0];
        // $user = User::find(1); // Mendapatkan pengguna dari database
        // $role = Role::where('name', 'owner')->first(); // Mendapatkan peran 'owner' dari database
        // $user->removeRole($role); // Mencabut peran 'owner' dari pengguna
        // $user->assignRole($role); // Menetapkan peran 'owner' ke pengguna
        // return User::find(1)->getRoleNames(); // Mendapatkan peran dari pengguna
        // $data_games = Games::where('status',1)->get();
    }
}
