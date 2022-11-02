<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeFormation extends Model
{
    use HasFactory;

    public $table = 'digit_parametrage_typeformation';


    public function categorieformation(){
        return $this->belongsTo(CategorieFormation::class,'categorieformation_id');
    }
}
