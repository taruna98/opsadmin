<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;


class UserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');

        // function kretech generate code profile
        function generateCode($length = 11)
        {
            $characters = 'abcdefghijklmnopqrstuvwxyz';
            $code = '';

            // generate the first character (3 or 4)
            $firstChar = (string) rand(3, 4);
            $code .= $firstChar;

            // generate the second and third characters (uppercase letters)
            $code .= chr(rand(65, 90)); // uppercase letter
            $code .= chr(rand(65, 90)); // uppercase letter

            // generate the rest of the characters
            $max = strlen($characters) - 1;
            for ($i = 0; $i < $length - 3; $i++) {
                $code .= $characters[rand(0, $max)];
            }

            return $code;
        }
    }

    public function index(Request $request)
    {
        $title = 'User';
        $users = User::with('roles')->get();
        $roles = Role::where('id', '!=', '1')->get();
        $rolez = Role::get();

        // $users = User::whereHas('roles', function($query) {
        //     $query->where('name', 'owner');
        // })->with('roles')->get();
        // return $users;

        if ($users != null) {
            // $data = json_decode($users, true);
            // $result = [];
            // foreach ($data as $item) {
            //     $user = [
            //         'id' => $item['id'],
            //         'name' => $item['name'],
            //         'email' => $item['email'],
            //         'email_verified_at' => $item['email_verified_at'],
            //         'is_active' => $item['is_active'],
            //         'created_at' => $item['created_at'],
            //         'updated_at' => $item['updated_at'],
            //         'role_id' => $item['roles'][0]['id'],
            //         'role_name' => $item['roles'][0]['name']
            //     ];
            //     $result[] = $user;
            // }
            $data2 = json_decode($roles, true);
            $result2 = [];
            foreach ($data2 as $item2) {
                $user2 = [
                    'id' => $item2['id'],
                    'name' => $item2['name'],
                    'guard_name' => $item2['guard_name'],
                    'created_at' => $item2['created_at'],
                    'updated_at' => $item2['updated_at']
                ];
                $result2[] = $user2;
            }
            $data3 = json_decode($rolez, true);
            $result3 = [];
            foreach ($data3 as $item3) {
                $user3 = [
                    'id' => $item3['id'],
                    'name' => $item3['name'],
                    'guard_name' => $item3['guard_name'],
                    'created_at' => $item3['created_at'],
                    'updated_at' => $item3['updated_at']
                ];
                $result3[] = $user3;
            }
        } else {
            // $result = [];
            $result2 = [];
            $result3 = [];
        }

        if ($request->ajax()) {
            // $users = User::with('roles');
            // return DataTables::of($users)->make(true);

            if (!empty($request->filter_role)) {
                // $data = DB::connection('mysql15')->table('player')
                //     ->select('id', 'player_id', 'game_id', 'uid', 'nickname', 'platform', 'cheat_attempt', 'apple', 'google', 'block', 'status', 'diamond', 'coin', 'star', 'lives', 'level_progress', 'vn_progress', 'booster', 'powerup', 'player_avatar', 'player_frame', 'created_at', 'updated_at')->where('block', $request->filter_role)->get();
                //     $count_data = count($data);

                $filter_role = $request->filter_role;
                $users = User::whereHas('roles', function ($query) use ($filter_role) {
                    $query->where('name', $filter_role);
                })->with('roles')->get();
                $count_data = count($users);
            } else {
                // $data = DB::connection('mysql15')->table('player')->select('id', 'player_id', 'game_id', 'uid', 'nickname', 'platform', 'cheat_attempt', 'apple', 'google', 'block', 'status', 'diamond', 'coin', 'star', 'lives', 'level_progress', 'vn_progress', 'booster', 'powerup', 'player_avatar', 'player_frame', 'created_at', 'updated_at')->where('block', '>', 0)->get();
                // $count_data = count($data);

                $users = User::with('roles')->get();
                $count_data = count($users);
            }

            return Datatables::of($users)->setTotalRecords($count_data)->setFilteredRecords(0)->make(true);

            // return DataTables::of($users)->make(true);
        }

        return view('admin_user', [
            'title' => $title,
            // 'users' => $result,
            'roles' => $result2,
            'rolez' => $result3
        ]);
    }

    public function store(Request $request)
    {
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

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $request->validate([
            'edit_id'               => 'required',
            'edit_name'             => 'required|max:50',
            'edit_img_profile'      => 'image|mimes:jpg|max:2048'
        ]);

        // get user
        $user = User::where('email', $request->edit_email)->first();

        // get params
        $user_id    = Auth::user()->id;
        $module     = 'Admin';
        $scene      = 'User';
        $activity   = 'Edit - ' . $request->edit_email;
        $ip         = $request->ip();
        // ---
        $id         = $request->edit_id;
        $name       = $request->edit_name;
        $email      = $request->edit_email;
        $password   = ($request->edit_password == null) ? $user->password : Hash::make($request->edit_password);
        $role       = $request->edit_role;
        $image      = $request->file('edit_img_profile');
        $image_name = isset($image) ? 'admin_img_profile_' . strstr($email, '@', true) . '.' . $image->extension() : '';
        $status     = $request->edit_status;

        // // temp variable
        // $temp = [
        //     'user_id'   => $user_id,
        //     'module'    => $module,
        //     'scene'     => $scene,
        //     'activity'  => $activity,
        //     'ip'        => $ip,
        // // ---
        //     'id'        => $id,
        //     'name'      => $name,
        //     'email'     => $email,
        //     'password'  => $password,
        //     'role'      => $role,
        //     'image'     => $image_name,
        //     'status'    => $status
        // ];

        // update user
        $user_update = User::find($id);
        if (!$user_update) {
            Alert::error('Failed', 'Update User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        $user_update->name = $name;
        $user_update->email = $email;
        $user_update->password = $password;
        $user_update->is_active = $status;
        $user_update->save();
        $user_update->roles()->detach();
        $user_update->assignRole($role);

        // check image
        if ($image !== null || $image != '') {
            $image->move(public_path('assets/img'), $image_name);
        }

        $save_log_activity = LogActivity::saveLogActivity($user_id, $module, $scene, $activity, $ip);
        if (!$save_log_activity) {
            Alert::error('Failed', 'Create User')->showConfirmButton($btnText = 'OK', $btnColor = '#DC3545')->autoClose(3000);
            return redirect()->back();
        }

        Alert::success('Success', 'Update User')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
    }

    public function detail($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }

    public function roles()
    {
        $roles = Role::get();
        return response()->json($roles);
    }
}
