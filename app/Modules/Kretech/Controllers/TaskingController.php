<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class TaskingController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Kretech Tasking';
        $email = Auth::user()->email;
        $user_id = Auth::user()->id;
        $role = Auth::user()->roles->pluck('name')[0];

        if ($role == 'owner' || $role == 'admin') {

            if ($request->ajax()) {
                $kretech_tasking = DB::connection('mysql')->table('tasking')->get();
                $count_data = count($kretech_tasking);

                return Datatables::of($kretech_tasking)->setTotalRecords($count_data)->setFilteredRecords(0)->make(true);
            }

            return view('Kretech::kretech_tasking', [
                'title' => $title
            ]);
        } else {
            // not for user / member
        }
    }

    public function detail($id)
    {
        // get tasking
        $kretech_tasking = DB::connection('mysql')->table('tasking')->where('id', $id)->first();
        if (!$kretech_tasking) {
            return response('tasking not found', 404);
        }

        return response()->json($kretech_tasking);
    }
}
