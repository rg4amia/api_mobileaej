<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffreEmploi extends Model
{
    /* public $belongsToMany = [
        'situationmatrimoniale' => [
            'Digit\Parametrage\Models\SituationMatrimonialeModel',
            'table' => 'digit_offreformation_situationmatrimoniale_offreemploi',
            'key' => 'offreemploi_id',
            'otherKey' => 'situationmatrimoniale_id'
        ],
        'sexes' => [
            'Digit\Parametrage\Models\SexeModel',
            'table' => 'digit_offreformation_sexe_emploi',
            'key' => 'emploi_id',
            'otherKey' => 'sexe_id'
        ],
        'specialites' => [
            'Digit\Parametrage\Models\SecteurActiviteModel',
            'table' => 'digit_offreformation_specialite_offreemploi',
            'key' => 'offreemploi_id',
            'otherKey' => 'specialite_id'
        ],
    ];
*/
    protected $table = 'digit_offreformation_offreemploi';

    public function typecontrat(){
        return $this->belongsTo('App\Models\TypeContrat','typecontrat_id','id');
    }

    public function diplome(){
        return $this->belongsTo('App\Models\Diplome','diplome_id','id');
    }

    public function agenceregionale(){
        return $this->belongsTo('App\Models\AgenceRegionale','divisionregionaleaej_id','id');
    }

    public function sexe(){
        return $this->belongsToMany('App\Models\Sexe','digit_offreformation_sexe_emploi','emploi_id','sexe_id');
    }

    public function niveauetude(){
        return $this->belongsTo('App\Models\NiveauEtude','niveauetude_id','id');
    }

    public function specialites(){
        return $this->belongsToMany('App\Models\SecteurActivite','digit_offreformation_specialite_offreemploi','offreemploi_id','specialite_id');
    }

}
