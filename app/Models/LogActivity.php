<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    protected $connection   = 'mysql';
    protected $table        = 'log_activity';
    protected $guarded      = ['id'];

    public static function saveLogActivity($user_id, $module, $scene, $activity, $ip)
    {
        return self::create([
            'user_id'   => $user_id,
            'module'    => $module,
            'scene'     => $scene,
            'activity'  => $activity,
            'ip'        => $ip
        ]);
    }
}
