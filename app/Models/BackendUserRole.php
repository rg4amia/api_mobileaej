<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackendUserRole extends Model
{
    use HasFactory;

    protected $table = 'backend_user_roles';

    public function backendusers(){
        return $this->hasMany(BackendUser::class);
    }
}
