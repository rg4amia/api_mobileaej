<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    use HasFactory;
    protected $table = 'digit_parametrage_commune';

    public function agenceregionale(){
        $this->belongsTo(AgenceRegionale::class,'divisionregionaleaej_id');
    }

    public function ville(){
        $this->belongsTo(Ville::class,'ville_id');
    }
}
