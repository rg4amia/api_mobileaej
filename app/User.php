<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'surname',
        'username',
        'email',
        'cellulaire',
        'demandeuremploi_id',
        'typeutilisateur_id',
        'is_activated',
        'reset_password_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function demandeur()
    {
        return  $this->belongsTo('App\Models\DemandeurEmploi', 'demandeuremploi_id', 'id');
    }

    static function loadDemandeur()
    {
        ini_set('memory_limit', -1);
        $query = null;
        $query = static::whereHas('demandeur', function ($query) {
            //$query->whereNotIn('categorieformation_id', [$this->categorie_formation_fcq, $this->categorie_formation_mon_passseport]);
        })->get();

        return $query;
    }
}
