<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffreEmploiDemandeur extends Model
{
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
        return  $this->belongsTo('App\Models\OffreEmploi','offreemploi_id','id');
    }

    public function demandeur(){
        return  $this->belongsTo('App\Models\DemandeurEmploi','demandeur_id','id');
    }

    public function listeDemandesEmploi(){
        $demandeur = auth()->user()->demandeur;
        return static::with('demandeur', 'offreemploi')->where('demandeur_id',$demandeur->id)->paginate(10);
    }

    // statut du demandeur ?? afficher lors de la proc??dure de traitement de l'offre d'emploi
    public function getPostulantStatutAttribute() {

        if ($this->offreemploi->statutoffre_id == 1)
            return "Traitement en cours";

        // Fin de la pr??s??lection AEJ
        if ($this->offreemploi->statutoffre_id == 6 && $this->estpreselectionneaej == 1)
            return "Pr??s??lectionn??(e)";

        if ($this->offreemploi->statutoffre_id == 6 && $this->estenreserve == 1)
            return "En r??serve";

        if ($this->offreemploi->statutoffre_id == 6)
            return "R??fus??(e)";

        // Fin s??lection AEJ
        if ($this->offreemploi->statutoffre_id == 3 && $this->estretenu == 1)
            return "Retenu(e) par l'AEJ";

        if ($this->offreemploi->statutoffre_id == 3 && $this->estenreserve == 1)
            return "En r??serve";

        if ($this->offreemploi->statutoffre_id == 3)
            return "Non retenu(e) par l'AEJ";

        if (in_array($this->offreemploi->typecontrat_id, [3, 7, 16]) && $this->offreemploi->statutoffre_id == 7 && $this->estretenu == 1)
            return "Retenu(e)";

        //le cas ??ch??ant pour les offres de stage
        if (in_array($this->offreemploi->typecontrat_id, [3, 7, 16]) && $this->offreemploi->statutoffre_id == 7)
            return "Non retenu(e)";

        // Fin s??lection entreprise
        if ($this->offreemploi->statutoffre_id == 7 && $this->estretenuentreprise == 1)
            return "Retenu(e)";

        if ($this->offreemploi->statutoffre_id == 7)
            return "Non retenu(e)";

        // le cas ??ch??ant
        return "Non retenu(e)";
    }

    public function getProgressionAttribute() {

        if (in_array($this->postulantStatut, ["Retenu(e)"]))
            return "success";

        if (in_array($this->postulantStatut, ["Traitement en cours", "Pr??s??lectionn??(e)", "Retenu(e) par l'AEJ"]))
            return "info";

        if (in_array($this->postulantStatut, ["En r??serve"]))
            return "warning";

        if (in_array($this->postulantStatut, ["Non retenu(e)", "R??fus??(e)"]))
            return "danger";
    }
}
