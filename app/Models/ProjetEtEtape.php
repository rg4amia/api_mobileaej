<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjetEtEtape extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'digit_projetfinancement_projetfinancement_etapeprojetfinancement';

/*    public $fillable = ['etapeprojetfinancement_id',
        'commentaire','last_user','first_user','statutprojet_id','etapeprojetfinancement_id',
        'projetfinancement_id','notecomite','note','commentaire','actif'
        ];*/

    public function etape() {
        return $this->belongsTo('App\Models\EtapeProjetFinancement','etapeprojetfinancement_id','id');
    }

    public function projetfinancement() {
        return $this->belongsTo('App\Models\ProjetFinancement','projetfinancement_id','id');
    }

    public function analyse(){
        return $this->hasMany('App\Models\Analyse','etape_id');
    }

}
