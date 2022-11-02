<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialiteDiplome extends Model
{
    protected $table        = 'digit_parametrage_specialitediplome';
    protected $fillables    = ['libelle','description'];
}
