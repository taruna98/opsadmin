<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'User';

        if ($request->ajax()) {
            $filter_role = 'kretech member';
            $users = User::whereHas('roles', function ($query) use ($filter_role) {
                $query->where('name', $filter_role);
            })->with('roles')->get();
            $count_data = count($users);

            return Datatables::of($users)->setTotalRecords($count_data)->setFilteredRecords(0)->make(true);
        }

        return view('Kretech::kretech_user', [
            'title' => $title
        ]);
    }

    public function detail($id)
    {
        $title = 'Detail User';

        // get user from table users
        $user = User::where('id', $id)->first();
        
        // verify user from table profile
        $profile = DB::connection('mysql2')->table('profiles')->where('eml', $user->email)->first();
        if ($profile == null) {
            Alert::error('Failed', 'User Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        // declare variable
        $api_url = env('API_URL');
        $code = $profile->cod;

        // join data profile
        $profile->nme = $user->name;
        $profile->stt = $user->is_active;

        // get data profile from api
        $get_profile = Http::get($api_url . 'profile/' . $code);

        // check response
        if ($get_profile == '[]') {
            Alert::error('Failed', 'Profile Not Found')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        return view('Kretech::kretech_user_detail', [
            'title' => $title,
            'user'  => response()->json($profile)
        ]);
    }
}
