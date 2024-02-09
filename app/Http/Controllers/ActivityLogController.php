<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class ActivityLogController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title          = 'Activity Log';
        $log_activity   = LogActivity::join('users', 'log_activity.user_id', '=', 'users.id')->select('users.name', 'log_activity.*')->orderBy('log_activity.created_at', 'desc')->get();
        
        return view('admin_activity_log', [
            'title'         => $title,
            'log_activity'  => $log_activity
        ]);
    }
}
