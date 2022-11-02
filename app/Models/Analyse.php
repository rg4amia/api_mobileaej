<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analyse extends Model
{
    use HasFactory;
    public $table = 'digit_projetfinancement_analyse';


    public function  projetfinancement(){
        return $this->belongsTo(ProjetFinancement::class,'projetfinancement_id','id');
    }

    public function etape(){
        return $this->belongsTo(ProjetEtEtape::class,'etape_id','id');
    }
}
