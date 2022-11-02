<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetFinancement extends Model
{
    use HasFactory;
    protected $table = 'digit_projetfinancement_projetfinancement';

    protected $fillable = [
        'matriculeprojet','descriptionactivite','estnouvelleactivite',
        'intituleprojet','nombreemploi','sigle','raisonsociale',
        'quartierprojet','justificationprojet','motifnonremboursementpret',
        'nouvelleactivite','beneficiarepret','justificationprojet','concurrent',
        'autresecteuractivite','objetdemande','typeprogramme_id','district_id' ,
        'clientspotentielsetconcurrent','besointechnique','autrebesoin','coutprojet',
        'secteuractivite_id','fournisseur','planmarketing','produitservice',
        'formejuridique_id','region_id','ville_id','descriptionprocessusproduction',
        'commune_id','descriptionproduitoffert','sourceapprovisionnement',
        'caffaireprevisionnel1','caffaireprevisionnel2','caffaireprevisionnel3',
        'caffaireprevisionnel4','caffaireprevisionnel5','descriptionexperiencedansactivite',
        'chargeprevisionnelle1','chargeprevisionnelle2','statut_id','status_projet',
        'chargeprevisionnelle3','chargeprevisionnelle4','chargeprevisionnelle5',
        'resultatnet1','resultatnet2','resultatnet3','resultatnet4','resultatnet5',
        'typeprojet_id','demandeur_id','connaissanceactivite','divisionregionaleaej_id'
        ,'comptecontribuable','registrecommerce','marchesclient',
        'comptebancaire','descriptionprocessusproduction','montantpret',
        'pretrembourse', 'user_affect','benefice_1','benefice_2','benefice_3','check_update','corbeille_status',
        'planaffaire','titreprojet','traitementaeffectuer_id','comitecertif_pv','comitecertif_rapport',
        'file_notificationaccord','file_actesnantissement','file_tableauamortissement','file_contratpret','file_actesgarantieadditionnel',
        'statusactedegarantiesigne','file_plandecaissement','file_ficherecapitulative'
    ];

    public $casts = [
        'statusactedegarantiesigne' => 'boolean',
        'file_plandecaissement'     => 'string',
        'file_ficherecapitulative'  => 'string'
    ];

    public function scopeMine(){
         $demandeur_id = auth()->user()->demandeur->id;
        return static::where('demandeur_id', '=', $demandeur_id);
    }

    public function demandeur() {
        return $this->belongsTo(DemandeurEmploi::class,'demandeur_id','id');
    }

    public function secteuractivite() {
        return $this->belongsTo(SecteurActivite::class,'secteuractivite_id','id');
    }

    public function region() {
        return $this->belongsTo(Region::class,'region_id','id');
    }

    public function formejuridique() {
        return $this->belongsTo(FormeJuridique::class,'formejuridique_id','id');
    }

    public function ville() {
        return $this->belongsTo(Ville::class,'ville_id','id');
    }

    public function typeprojet() {
        return $this->belongsTo(TypeProjet::class,'typeprojet_id','id');
    }

    public function traitementaeffectuer() {
        return $this->belongsTo(EtapeProjetFinancement::class,'traitementaeffectuer_id','id');
    }

    public function statutprojet() {
        return $this->belongsTo(StatutProjet::class,'statut_id','id');
    }

    public function divisionregionaleaej() {
        return $this->belongsTo(AgenceRegionale::class,'divisionregionaleaej_id','id');
    }

    public function commune() {
        return $this->belongsTo(Commune::class,'commune_id','id');
    }

    public function typeprogramme() {
        return $this->belongsTo(TypeProgramme::class,'typeprogramme_id','id');
    }

    public function district() {
        return $this->belongsTo(District::class,'district_id','id');
    }

    public function derniertraitement() {
        return $this->belongsTo(ProjetEtEtape::class,'derniertraitement_id','id');
    }

    public function statut(){
        return $this->belongsTo(StatutProjet::class,'statut_id','id');
    }

    public function etapefinancement(){
        return $this->belongsTo(ProjetEtEtape::class,'projetfinancement_id','id');
    }

    public function etapesdevalidation(){
        return $this->hasMany(ProjetEtEtape::class,'projetfinancement_id','id');
    }

}
