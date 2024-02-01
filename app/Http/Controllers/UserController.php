<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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
        } else {
            $result = [];
        }

        return view('admin_user', [
            'title' => $title,
            'users' => $result
        ]);
    }
}
