<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecteurActivite extends Model
{
    use HasFactory;

  //local ://  public $table = 'digit_parametrage_secteuractivite';
    public $table = 'digit_parametrage_secteuractivite1';

    public function soussecteuractivites(){
        return $this->hasMany(SousSecteur::class,'secteuractivite_id','id');
    }
}
