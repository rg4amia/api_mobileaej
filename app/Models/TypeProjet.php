<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeProjet extends Model
{
    use HasFactory;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'digit_parametrage_typeprojet';
}
