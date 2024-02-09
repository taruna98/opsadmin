<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title = 'User';
        $users = User::with('roles')->get();
        $roles = Role::where('id', '!=', '1')->get();
        $rolez = Role::get();

        if ($users != null) {
            $data = json_decode($users, true);
            $result = [];
            foreach ($data as $item) {
                $user = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'email_verified_at' => $item['email_verified_at'],
                    'is_active' => $item['is_active'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                    'role_id' => $item['roles'][0]['id'],
                    'role_name' => $item['roles'][0]['name']
                ];
                $result[] = $user;
            }
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
            $result = [];
            $result2 = [];
            $result3 = [];
        }

        return view('admin_user', [
            'title' => $title,
            'users' => $result,
            'roles' => $result2,
            'rolez' => $result3
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'create_name'      => 'required|max:50',
            'create_email'     => 'required|email',
            'create_password'  => 'required'
        ]);

        // get params
        $name       = $request->create_name;
        $email      = $request->create_email;
        $password   = Hash::make($request->create_password);
        $role       = $request->create_role;
        $status     = $request->create_status;

        // // temp variable
        // $temp = [
        //     'name'      => $name,
        //     'email'     => $email,
        //     'password'  => $password,
        //     'role'      => $role,
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
            'edit_id'   => 'required',
            'edit_name' => 'required|max:50'
        ]);

        // get user
        $user = User::where('email', $request->edit_email)->first();

        // get params
        $id         = $request->edit_id;
        $name       = $request->edit_name;
        $email      = $request->edit_email;
        $password   = ($request->edit_password == null) ? $user->password : Hash::make($request->edit_password);
        $role       = $request->edit_role;
        $status     = $request->edit_status;

        // // temp variable
        // $temp = [
        //     'id'        => $id,
        //     'name'      => $name,
        //     'email'     => $email,
        //     'password'  => $password,
        //     'role'      => $role,
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

        Alert::success('Success', 'Update User')->showConfirmButton($btnText = 'OK', $btnColor = '#0D6EFD')->autoClose(3000);
        return redirect()->back();
    }

    public function detail($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }
}
