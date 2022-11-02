<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffreEmploiDemandeur extends Model
{
    use HasFactory;
    public $demandeur;

    public $table = 'digit_demandeur_offreemploi_demandeur';

   //    public $belongsTo = [
    //        'demandeur' => ['Digit\Demandeur\Models\DemandeurEmploiModel', 'Key' => 'demandeur_id', 'otherKey' => 'id'],
    //        'demandeurSansEmploi' => ['Digit\Demandeur\Models\DemandeurEmploiModel', 'Key' => 'demandeur_id', 'otherKey' => 'id'/* ,
    //          'conditions' => 'statudemandeur_id != 3' */],
    //        'commune' => ['Digit\Parametrage\Models\CommuneModel', 'key' => 'commune_lieu_stage_id', 'otherKey' => 'id'],
    //        'statutmisenstage' => ['Digit\Parametrage\Models\StatutMiseEnStage', 'key' => 'statut_mise_en_stage_id', 'otherKey' => 'id'],
    //        // 'directionaccueil' => ['Digit\Parametrage\Models\DivisionRegionaleAejModel', 'key' => 'direction_acceuil_id', 'otherKey' => 'id'],
    //        'offreemploi' => ['Digit\OffreFormation\Models\OffreEmploiModel',
    //            'Key' => 'offreemploi_id',
    //            'otherKey' => 'id',
    //        //'scope' => 'courants'
    //        ],
    //    ];
    //    public $hasOne = [
    //        'suivistage' => [
    //            'Digit\Offreformation\Models\SuiviStageModel',
    //            'key' => 'postulant_id'
    //        ],
    //        'satisfaction' => [
    //            'Digit\Offreformation\Models\SatisfactionEmploiModel',
    //            'key' => 'postulant_id'
    //        ]
    //    ];
    //    public $hasMany = [
    //        'listedesvisites' => [
    //            'Digit\Offreformation\Models\VisiteSuiviStageModel',
    //            'key' => 'postulant_id',
    //        ]
    //    ];

    public function offreemploi(){
        return  $this->belongsTo(OffreEmploi::class,'offreemploi_id','id');
    }

    public function demandeur(){
        return  $this->belongsTo(DemandeurEmploi::class,'demandeur_id','id');
    }

    public function listeDemandesEmploi(){
        $demandeur = auth()->user()->demandeur;
        return static::with('demandeur', 'offreemploi')->where('demandeur_id',$demandeur->id)->paginate(10);
    }

    // statut du demandeur à afficher lors de la procédure de traitement de l'offre d'emploi
    public function getPostulantStatutAttribute() {

        if ($this->offreemploi->statutoffre_id == 1)
            return "Traitement en cours";

        // Fin de la présélection AEJ
        if ($this->offreemploi->statutoffre_id == 6 && $this->estpreselectionneaej == 1)
            return "Présélectionné(e)";

        if ($this->offreemploi->statutoffre_id == 6 && $this->estenreserve == 1)
            return "En réserve";

        if ($this->offreemploi->statutoffre_id == 6)
            return "Réfusé(e)";

        // Fin sélection AEJ
        if ($this->offreemploi->statutoffre_id == 3 && $this->estretenu == 1)
            return "Retenu(e) par l'AEJ";

        if ($this->offreemploi->statutoffre_id == 3 && $this->estenreserve == 1)
            return "En réserve";

        if ($this->offreemploi->statutoffre_id == 3)
            return "Non retenu(e) par l'AEJ";

        if (in_array($this->offreemploi->typecontrat_id, [3, 7, 16]) && $this->offreemploi->statutoffre_id == 7 && $this->estretenu == 1)
            return "Retenu(e)";

        //le cas échéant pour les offres de stage
        if (in_array($this->offreemploi->typecontrat_id, [3, 7, 16]) && $this->offreemploi->statutoffre_id == 7)
            return "Non retenu(e)";

        // Fin sélection entreprise
        if ($this->offreemploi->statutoffre_id == 7 && $this->estretenuentreprise == 1)
            return "Retenu(e)";

        if ($this->offreemploi->statutoffre_id == 7)
            return "Non retenu(e)";

        // le cas échéant
        return "Non retenu(e)";
    }

    public function getProgressionAttribute() {

        if (in_array($this->postulantStatut, ["Retenu(e)"]))
            return "success";

        if (in_array($this->postulantStatut, ["Traitement en cours", "Présélectionné(e)", "Retenu(e) par l'AEJ"]))
            return "info";

        if (in_array($this->postulantStatut, ["En réserve"]))
            return "warning";

        if (in_array($this->postulantStatut, ["Non retenu(e)", "Réfusé(e)"]))
            return "danger";
    }
}
