<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DemandeurEmploi extends Model
{

    protected $table = 'digit_demandeur_demandeuremploi';

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'nom','prenoms','telephone','sexe_id','paysdemandeur_id','paysnationalite_id','conseilleremploi_id ',
        'typepieceidentite_id','diplome_id','specialite_id','divisionregionaleaej_id',
        'villeresidence_id','nomdupere','nomdelamere','situationmatrimoniale_id','statudemandeur_id',
        'typesituationhandicap_id','lieuhabitation_id','dateinscription','lieunaissance_id',
        'datenaissance','numerocni','matriculeaej','cvdemandeur','ancienphoto','diplomefile','cnifile',
        'cvfile', 'guichetemplois_id', 'dup_competence','onesignale_id'
    ];


    protected $cast = [
        'onesignale_id' => 'array'
    ];

    /**
     * @var array List of attribute names which should be set to null when empty.
     */

    /*
    protected $nullable = [
        'nocnps', 'expertise','datedebutchomage', 'datenaissance', 'telephone', 'etablissementfrequente', 'dateinscription',
        'nomdupere', 'nomdelamere', 'precision_handicap', 'migration_key', 'cvdemandeur', 'offresms', 'offreemail', 'email',
        'precisionhandicap', 'ancienphoto',
        'handicapdemandeur'
    ];
    */


    //'niveauetude' => ['Digit\Parametrage\Models\NiveauEtudeModel', 'key' => 'niveauetude_id', 'otherKey' => 'id'],

    public function niveauetude(){
        return $this->belongsTo('App\Models\NiveauEtude','niveauetude_id','id');
    }

    public function agenceregionale(){
        return $this->belongsTo('App\Models\AgenceRegionale','divisionregionaleaej_id','id');
    }

    public function guichetemploi(){
        return $this->belongsTo('App\Models\GuichetEmploi','guichetemplois_id','id');
    }

    public function conseilleremploi(){
        return $this->belongsTo('App\Models\BackendUser','conseilleremploi_id','id');
    }

    public function typepieceidentite(){
        return $this->belongsTo('App\Models\TypePieceIdentite','typepieceidentite_id','id');
    }

    public function diplome(){
        return $this->belongsTo('App\Models\Diplome','diplome_id','id');
    }

    public function specialite(){
        return $this->belongsTo('App\Models\SpecialiteDiplome','specialite_id','id');
    }

    public function statudemandeur(){
        return $this->belongsTo('App\Models\Statutdemandeur','statudemandeur_id','id');
    }

    public function soussecteuractivite(){
        return $this->belongsTo('App\Models\SousSecteur','soussecteuractivite_id','id');
    }

    public function uniteexperience(){
        return $this->belongsTo('App\Models\UniteExperience','uniteexperience_id','id');
    }

    public function photoProfile()
    {
        return $this->ancienphoto
           ? Storage::disk('photodemandeur')->url($this->ancienphoto)
            : 'https://www.gravatar.com/avatar/'.md5(strtolower(trim(auth()->user()->email)));
    }

    public function getDiplome()
    {
        return $this->diplomefile
            ? Storage::disk('diplomedemandeur')->url($this->diplomefile)
            : 'https://www.gravatar.com/avatar/'.md5(strtolower(trim(auth()->user()->email)));
    }

    public function getCv()
    {
        return $this->cvfile
            ? Storage::disk('cvdemandeur')->url($this->cvfile)
            : 'https://www.gravatar.com/avatar/'.md5(strtolower(trim(auth()->user()->email)));
    }

/*    public function getCni()
    {
        return $this->cnifile
            ? Storage::disk('cnidemandeur')->url($this->cnifile)
            : 'https://www.gravatar.com/avatar/'.md5(strtolower(trim(auth()->user()->email)));
    }*/


    public function getCni()
    {
        return $this->cnifile
            ? Storage::disk('cnidemandeur')->url($this->cnifile)
            : asset('app-assets/images/avatars/avatar_login.jpg');
    }


    public function user(){
        return $this->belongsTo('App\Models\User','id', 'demandeuremploi_id');
    }

}
