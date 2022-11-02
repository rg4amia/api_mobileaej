<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavorisOffreEmploi extends Model
{

    protected $fillable= [
        'diplome','niveauetude',
        'typecontrat','agenceregionale',
        'specialite','user_id', 'demandeur_id'
    ];

    protected $casts = [
        'specialite'        => 'array',
        'diplome'           => 'array',
        'niveauetude'       => 'array',
        'typecontrat'       => 'array',
        'agenceregionale'   => 'array',
        'user_id'           => 'integer',
        'demandeur_id'      => 'integer',
    ];

    public function specialite(){
        return $this->belongsToMany('App\Models\SecteurActivite','favoris_offre_emploi_secteur_actvites','favoris_id','secteuractivite_id');
    }
}
