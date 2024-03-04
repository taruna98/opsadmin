<?php

namespace Modules\Kretech\Controllers;

use App\Models\LogActivity;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ActivityController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title      = 'Activity';
        $activity   = LogActivity::join('users', 'log_activity.user_id', '=', 'users.id')->select('users.name', 'log_activity.*')->where('log_activity.module', 'Kretech')->orderBy('log_activity.created_at', 'desc')->get();
        
        return view('Kretech::kretech_activity', [
            'title'     => $title,
            'activity'  => $activity
        ]);
    }
}
