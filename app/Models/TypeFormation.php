<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeFormation extends Model
{

    public $table = 'digit_parametrage_typeformation';

    public function categorieformation(){
        return $this->belongsTo('App\Models\CategorieFormation','categorieformation_id');
    }
}
