<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use App\Models\OffreEmploiDemandeur;
use App\Models\OffreFormationDemandeur;
use App\Models\ProjetFinancement;
use Illuminate\Http\Request;

class SuivieDemandeController extends Controller
{
    public function suiviePostulation(){
        $data = [];
       $demandeurEmploi =  new OffreEmploiDemandeur();
       $emploidemdeurs = $demandeurEmploi->listeDemandesEmploi();
        //\Carbon\Carbon::parse($item->datepublication)->translatedFormat('d M Y');
        foreach ($emploidemdeurs as $item) {
            if($item->offreemploi)
                $data[]= [
                    'noreference'       => $item->offreemploi->noreference,
                    'intitule'          => $item->offreemploi->intitule,
                    'typecontrat'       => $item->offreemploi->typecontrat->libelle,
                    'datecreation'      => \Carbon\Carbon::parse($item->offreemploi->created_at)->translatedFormat('d M Y'),
                    'progression'       => $item->progression,
                    'postulantstatut'   => $item->postulantStatut,
                ];
       }

        return response()->json($data);
    }

    public function suivieFormationFCQ(){
        $data = [];
       $demandeurformationfcq =  new OffreFormationDemandeur();
       $formationdemdeurs = $demandeurformationfcq->loadFormationsFcq();

        foreach ($formationdemdeurs as $item) {
            if($item->formation)
                $data[]= [
                    'id'                => $item->id,
                    'noreference'       => $item->formation->reference,
                    'intitule'          => $item->formation->intitule,
                    'datecreation'      => \Carbon\Carbon::parse($item->formation->created_at)->translatedFormat('d M Y'),
                    'progression'       => $item->progression,
                    'postulantstatut'   => $item->postulantStatut,
                ];
       }

        return response()->json($data);
    }

    public function suivieFormationMPPE(){
        $data = [];
       $demandeurformationfcq =  new OffreFormationDemandeur();
       $formationdemdeurs = $demandeurformationfcq->loadFormationsMppe();

        foreach ($formationdemdeurs as $item) {
            if($item->formation)
                $data[]= [
                    'id'                => $item->id,
                    'noreference'       => $item->formation->reference,
                    'intitule'          => $item->formation->intitule,
                    'datecreation'      => \Carbon\Carbon::parse($item->formation->created_at)->translatedFormat('d M Y'),
                    'progression'       => $item->progression,
                    'postulantstatut'   => $item->postulantStatut,
                ];
       }

        return response()->json($data);
    }

    public function suivieProjetFinancement(){
       $projets = ProjetFinancement::mine()->get();
       $data = [];
        foreach ($projets as $projet) {
            $data[]=[
                'id'                => $projet->id,
                'matriculeprojet'   => $projet->matriculeprojet,
                'intituleprojet'    => $projet->intituleprojet,
                'datecreation'      => \Carbon\Carbon::parse($projet->created_at)->translatedFormat('d M Y'),
                'statut'            => $this->infostatusprojet($projet->statut_id),
            ];
        }

        return response()->json($data);
    }

    function infostatusprojet($statut_id){
        switch ($statut_id){
            case '':
                $info = "Transmis";
                break;
            case '1':
                $info = "Transmis";
                break;
            case '2':
                $info = "Traitement";
                break;
            case '3':
                $info = "Validé";
                break;
            case '4':
                $info = "Ajourné";
                break;
            default:
                $info = "Rejeté";
                break;
        }

        return $info;
    }


}
