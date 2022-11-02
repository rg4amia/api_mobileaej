<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersGroups extends Model
{

    protected $table = 'users_groups';
    protected $fillable = ["use_id", "user_group_id"];
    public $timestamps = false;
}
