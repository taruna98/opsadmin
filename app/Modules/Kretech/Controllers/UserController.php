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

        // lanjut detail user

        $user = User::with('roles')->findOrFail($id);
        return $user;
        return response()->json($user);
        return view('Kretech::kretech_user', [
            'title' => $title
        ]);
    }
}
