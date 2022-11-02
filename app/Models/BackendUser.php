<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class BackendUser extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'login',
        'email',
        'password',
        'telephonebureau',
        'skype',
        'sexe_id',
        'activation_code',
        'persist_code',
        'reset_password_code',
        'permissions',
        'is_activated',
        'role_id',
        'activated_at',
        'last_login',
        'last_activity',
        'is_superuser',
        'divisionregionaleaej_id',
        'migration_key',
        'actif',
        'cellulaire',
        'photo',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guard = 'backend';
    //protected $guard_name = 'backend';

    public function demandeurs()
    {
        return  $this->hasMany('App\Models\DemandeurEmploi', 'id','conseilleremploi_id');
    }

    public function agenceregionale(){
        return $this->belongsTo('App\Models\AgenceRegionale', 'divisionregionaleaej_id','id');
    }

    static function demandeursByConseiller()
    {
        return static::with('demandeur')->get();
    }

    public function photo()
    {
        return $this->photo
            ? Storage::disk('photobackend')->url($this->photo)
            : 'https://www.gravatar.com/avatar/'.md5(strtolower(trim(auth()->guard('backend')->user()->email)));
    }

    public function backendrole(){
        return $this->belongsTo('App\Models\BackendUserRole','role_id','id');
    }
}
