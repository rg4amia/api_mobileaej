<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecteurActivite extends Model
{

  //local ://  public $table = 'digit_parametrage_secteuractivite';
    public $table = 'digit_parametrage_secteuractivite1';

    public function soussecteuractivites(){
        return $this->hasMany('App\Models\SousSecteur','secteuractivite_id','id');
    }
}
