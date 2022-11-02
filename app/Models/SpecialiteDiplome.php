<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialiteDiplome extends Model
{
    use HasFactory;

    protected $table        = 'digit_parametrage_specialitediplome';
    protected $fillables    = ['libelle','description'];
}
