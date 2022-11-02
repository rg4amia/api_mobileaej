<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackendUserRole extends Model
{

    protected $table = 'backend_user_roles';

    public function backendusers(){
        return $this->hasMany('App\Models\BackendUser');
    }
}
