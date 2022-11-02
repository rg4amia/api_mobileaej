<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $table = 'digit_offreformation_formation';


    /*  public $belongsTo = [
        'unitedureeformation' => ['Digit\Parametrage\Models\UniteExperienceController', 'key' => 'unitedureeformation_id', 'otherKey' => 'id'],
        'diplome' => ['Digit\Parametrage\Models\DiplomeModel', 'key' => 'diplome_id', 'otherKey' => 'id'],
        'niveauetude' => ['Digit\Parametrage\Models\NiveauEtudeModel', 'key' => 'niveauetude_id', 'otherKey' => 'id'],
        'secteuractivite' => ['Digit\Parametrage\Models\SecteurActiviteModel', 'key' => 'secteuractivite_id', 'otherKey' => 'id'],
        'typeformation' => ['Digit\Parametrage\Models\TypeFormationModel', 'key' => 'typeformation_id', 'otherKey' => 'id'],
        'categorieformation' => ['Digit\Parametrage\Models\CategorieFormationModel', 'key' => 'categorieformation_id', 'otherKey' => 'id'],
        'moduleformation' => ['Digit\Parametrage\Models\ModuleFormationModel', 'key' => 'moduleformation_id', 'otherKey' => 'id'],
        'divisionregionaleaej' => ['Digit\Parametrage\Models\DivisionRegionaleAejModel', 'key' => 'divisionregionaleaej_id', 'otherKey' => 'id'],
    ];*/

    public function diplome(){
        return $this->belongsTo('App\Models\Diplome','diplome_id');
    }

    public function typeformation()
    {
        return $this->belongsTo('App\Models\TypeFormation','typeformation_id');
    }

    public function categorieformation()
    {
        return $this->belongsTo('App\Models\CategorieFormation','categorieformation_id','id');
    }

    public function niveauetude()
    {
        return $this->belongsTo('App\Models\NiveauEtude','niveauetude_id');
    }

    public function secteuractivite()
    {
        return $this->belongsTo('App\Models\SecteurActivite','secteuractivite_id');
    }

    public function sexe(){
        return $this->belongsToMany('App\Models\Sexe','digit_offreformation_sexe_formation','formation_id','sexe_id');
    }
}
