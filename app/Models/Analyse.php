<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analyse extends Model
{

    public $table = 'digit_projetfinancement_analyse';


    public function  projetfinancement(){
        return $this->belongsTo('App\Models\ProjetFinancement','projetfinancement_id','id');
    }

    public function etape(){
        return $this->belongsTo('App\Models\ProjetEtEtape','etape_id','id');
    }
}
