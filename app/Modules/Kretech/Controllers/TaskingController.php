<?php

namespace Modules\Kretech\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
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
                $kretech_tasking = DB::connection('mysql')->table('tasking')->where('module', 'Kretech')->get();

                $kretech_data = [];

                foreach ($kretech_tasking as $task) {
                    if ($task->scene == 'Register') {
                        $user_request = DB::connection('mysql')->table('user_requests')->select('email')->where('task_id', $task->id)->first();
                        $task->email = $user_request ? $user_request->email : null;
                    }
                    $kretech_data[] = $task;
                }

                $count_data = count($kretech_data);

                return Datatables::of($kretech_data)->setTotalRecords($count_data)->setFilteredRecords(0)->make(true);
            }

            return view('Kretech::kretech_tasking', [
                'title' => $title
            ]);
        } else {
            // not for user / member
        }
    }

    public function approved(Request $request)
    {
        // auth
        $auth = Auth::user();

        // declare variable
        $api_url = env('API_URL');

        // request ajax
        if ($request->ajax()) {
            // get field
            $user_id    = $auth->id;
            $module     = 'Kretech';
            $ip         = $request->ip();

            // approve user
            if ($request->input('action') == 'tasking approved') {

                // get variable
                $module     = 'Kretech';
                $scene      = 'Register';
                $ip         = $request->ip();
                // ---
                $email      = $request->input('email');
                $status     = $request->input('status');
                $activity   = 'Approve Request Web Profile - ' . $email;
                $password   = Hash::make('123456');
                $role       = 'kretech member';
                $status     = 3;
                $name       = preg_match('/^[^._\d]+/', $email, $matches) ? ucfirst($matches[0]) : 'User';

                // temp variable
                $temp = [
                    'user_id'   => $user_id,
                    'module'    => $module,
                    'scene'     => $scene,
                    'activity'  => $activity,
                    'ip'        => $ip,
                    // ---
                    'name'      => $name,
                    'email'     => $email,
                    'password'  => $password,
                    'role'      => $role,
                    'status'    => $status
                ];

                // parse params to api
                $approve_user = Http::post($api_url . 'profile/request', [
                    'email'     => $email,
                    'status'    => $status
                ]);

                if ($approve_user != 'success register user') {
                    return $approve_user;
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
                    return response('precondition failed', 412);
                }

                // get from table user_requests
                $get_user_requests = DB::connection('mysql')->table('user_requests')->where('email', $email)->where('status', 3)->first();

                // update tasking
                $update_tasking = DB::connection('mysql')->table('tasking')
                    ->where('id', $get_user_requests->task_id)
                    ->where('status', 3)
                    ->update([
                        'user_id'   => $user_create->id,
                        'admin_id'  => $user_id
                    ]);
                if ($update_tasking === 0) {
                    return response('precondition failed', 412);
                }

                // save log activity
                $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
                if (!$save_log_activity) {
                    return response('precondition failed', 412);
                }

                return $approve_user;
            }
        }
    }

    public function detail($id)
    {
        // get tasking
        $kretech_tasking = DB::connection('mysql')->table('tasking')->where('module', 'Kretech')->where('id', $id)->first();
        if (!$kretech_tasking) {
            return response('tasking not found', 404);
        }

        if ($kretech_tasking->scene == 'Register') {
            $user_request = DB::connection('mysql')->table('user_requests')->select('email')->where('task_id', $kretech_tasking->id)->first();
            $kretech_tasking->email = $user_request ? $user_request->email : null;
        } else {
            $kretech_tasking->email = null;
        }

        return response()->json($kretech_tasking);
    }
}
