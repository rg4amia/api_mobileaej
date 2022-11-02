<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DemandeurEmploi extends Model
{
    use HasFactory;
    protected $table = 'digit_demandeur_demandeuremploi';

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'nom','prenoms','telephone','sexe_id','paysdemandeur_id','paysnationalite_id','conseilleremploi_id ',
        'typepieceidentite_id','diplome_id','specialite_id','divisionregionaleaej_id',
        'villeresidence_id','nomdupere','nomdelamere','situationmatrimoniale_id','statudemandeur_id',
        'typesituationhandicap_id','lieuhabitation_id','dateinscription','lieunaissance_id',
        'datenaissance','numerocni','matriculeaej','cvdemandeur','ancienphoto','diplomefile','cnifile','cvfile', 'guichetemplois_id', 'dup_competence'
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
        return $this->belongsTo(NiveauEtude::class,'niveauetude_id','id');
    }

    public function agenceregionale(){
        return $this->belongsTo(AgenceRegionale::class,'divisionregionaleaej_id','id');
    }

    public function guichetemploi(){
        return $this->belongsTo(GuichetEmploi::class,'guichetemplois_id','id');
    }

    public function conseilleremploi(){
        return $this->belongsTo(BackendUser::class,'conseilleremploi_id','id');
    }

    public function typepieceidentite(){
        return $this->belongsTo(TypePieceIdentite::class,'typepieceidentite_id','id');
    }

    public function diplome(){
        return $this->belongsTo(Diplome::class,'diplome_id','id');
    }

    public function specialite(){
        return $this->belongsTo(SpecialiteDiplome::class,'specialite_id','id');
    }

    public function statudemandeur(){
        return $this->belongsTo(Statutdemandeur::class,'statudemandeur_id','id');
    }

    public function soussecteuractivite(){
        return $this->belongsTo(SousSecteur::class,'soussecteuractivite_id','id');
    }

    public function uniteexperience(){
        return $this->belongsTo(UniteExperience::class,'uniteexperience_id','id');
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
        return $this->belongsTo(User::class,'id', 'demandeuremploi_id');
    }

}
