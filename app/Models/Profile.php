<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Profile extends Model
{
    protected $connection   = 'mysql2';
    protected $table        = 'profiles';
    protected $guarded      = ['id'];
}
