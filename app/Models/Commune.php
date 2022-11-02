<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $table = 'digit_parametrage_commune';

    public function agenceregionale(){
        $this->belongsTo('App\Models\AgenceRegionale','divisionregionaleaej_id');
    }

    public function ville(){
        $this->belongsTo('App\Models\Ville','ville_id');
    }
}
