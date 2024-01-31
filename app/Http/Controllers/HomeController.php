<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


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

        // $user = User::find(1); // Mendapatkan pengguna dari database
        // $role = Role::where('name', 'kretech admin')->first(); // Mendapatkan peran 'owner' dari database
        // $user->removeRole($role); // Mencabut peran 'owner' dari pengguna
        // $user->assignRole($role); // Menetapkan peran 'owner' ke pengguna
        // return User::find(1)->getRoleNames(); // Mendapatkan peran dari pengguna
        
        // $data_games = Games::where('status',1)->get();

        return view('admin_home', [
            'title'         => $title,
            // 'data_games'    => $data_games
        ]);
    }
}
