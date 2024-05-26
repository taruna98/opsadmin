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

class UserRequestController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'User Request';

        if ($request->ajax()) {
            $user_request = DB::connection('mysql')->table('user_requests')->where('module', 'Kretech')->get();

            $count_data = count($user_request);

            return Datatables::of($user_request)->setTotalRecords($count_data)->setFilteredRecords(0)->make(true);
        }

        return view('Kretech::kretech_user_request', [
            'title' => $title
        ]);
    }
}
