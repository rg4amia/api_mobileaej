<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use App\Mail\MailOffreEmploiPostulation;
use App\Models\Formation;
use App\Models\OffreFormationDemandeur as AssociationOE;
use App\Models\TentativeOffreFormationDemandeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use function Log;

class OffreFormationController extends Controller
{

    public $tentativeOffreFormation;
    public $dejapostuleroffreretenue = false;

    public function __construct()
    {
       // $this->dejapostuleroffreretenue = false;
    }

    public function listOffreFormation(){

        $offreformations = Formation::with('diplome','typeformation','categorieformation','niveauetude','secteuractivite','sexe')
            ->whereDate('digit_offreformation_formation.datefin', '>=', date('Y-m-d'))
            ->where('digit_offreformation_formation.valider', '=', 1)
            ->where('digit_offreformation_formation.actif', 1)
            ->whereNull('digit_offreformation_formation.deleted_at')
            ->where(function($query){
                $query->whereNull('digit_offreformation_formation.archive')
                    ->orWhere('digit_offreformation_formation.archive', 0)
                    ->orWhere('digit_offreformation_formation.archive', '');
            })
            ->orderBy('digit_offreformation_formation.created_at', 'desc')
            ->get();

        $data = [];

        foreach($offreformations as $item){

             $associationoe = AssociationOE::where('demandeur_id', '=',  Auth::user()->demandeur->id)->where('formation_id', '=', $item->id)->first();

            $date1 = new DateTime($item->datedebut);
            $date2 = new DateTime(now());
            $interval = $date1->diff($date2);

            if($item->categorieformation->id == 1){
                $categorie = "Formation FCQ";
            }else{
                $categorie = "Formation MPPE";
            }

            $datedebut   = \Carbon\Carbon::parse($item->datedebut)->translatedFormat('d M Y');
            $datefin     = \Carbon\Carbon::parse($item->datefin)->translatedFormat('d M Y');
            $created_at  = \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y');

            $data[] =[
                'id'                        => $item->id,
                'intitule'                  => $item->intitule,
                'lieudetravail'             => $item->lieu,
                'noreference'               => $item->reference,
                'descriptiontachesposte'    => $item->description,
                'datedebutoffre'            => strtoupper($datedebut),
                'datefinoffre'              => strtoupper($datefin),
                'created_at'                => strtoupper($created_at),
                'categorieformation_id'     => $item->categorieformation_id,
                'typeformation_id'          => $item->typeformation_id,
                'typecontrat_libelle'       => $categorie,
                'typecontrat_codecolor'     => "green",
                'nbrjour'                   => $interval->days,
                'diplome'                   => $item->diplome->libelle ?? 'Aucun diplôme',
                'typeformation'             => $item->typeformation->libelle,
                'categorieformation'        => $item->categorieformation->libelle,
                'niveauetude'               => $item->niveauetude->libelle,
                'secteuractivite'           => $item->secteuractivite,
                'sexe'                      => $item->sexe,
                'postule'                   => $associationoe ? true : false,
            ];
        }

        return response()->json($data);
    }


    public function formationById($id){
        //resultat de l'état du demandeur par rapport à cette offre formation
        $demandeur = Auth::user()->demandeur;

        if ($demandeur){

            $this->tentativeOffreFormation = TentativeOffreFormationDemandeur::where('demandeur_id', $demandeur->id)->where('formation_id',$id)->first();
            $all_formation_fcq = AssociationOE::where('demandeur_id', '=', $demandeur->id)->get();
            if (!demandeurPeutPostulerFormation($all_formation_fcq)) {
                $this->dejapostuleroffreretenue = true;
            }

        }

        $associationoe = AssociationOE::where('demandeur_id', '=', $demandeur->id)->where('formation_id', '=', $id)->first();
        $formation = Formation::with('typeformation','niveauetude')->where('id', $id)->first();

        $item = [
            'tentativeoffreformation'       => $this->tentativeOffreFormation,
            'dejapostuleroffreretenue'      => $this->dejapostuleroffreretenue,
            'associationoe'                 => $associationoe,
            'formation'                     => $formation,
        ];

        return response()->json($item);
    }

    //permet de postuler a une formation fcq
    public function onFormationPostuleFCQ(Request $request) {

        try {

            $formation = Formation::where('id', '=', $request->formation_id)->first();

            $demandeur = Auth()->user()->demandeur;
            $user = Auth()->user();

            //si le demandeur n'a pas renseigné son profil on lui demande de renseigner son profil
            if ($demandeur->secteuractivite_id == 0) {
                $message =  [
                    'status'    => "failed",
                    'message'   => "Veuillez renseignez votre profil demandeur"
                ];
            }

            $associationoe = AssociationOE::where('demandeur_id', '=', $demandeur->id)->where('formation_id', '=', $formation->id)->first();
            if ($associationoe) {
                $message =  [
                    'status'    => "failed",
                    'message'   => "Vous avez déjà postuler à cette offre de formation !"
                ];
            }

            $this->postuleFormation($demandeur, $formation, $request,'fcq',$user);

            $message =  [
                'status'    => "success",
                'message'   => 'Votre demande a été prise en compte !'
            ];

        } catch (\Exception $e) {
            $message =  [
                'status'    => "failed",
                'message'   => "Formation Postulation: ". $e->getMessage()
            ];
        }

        return response()->json($message,200);
    }

    public function onFormationPostulePasseport(Request $request) {

        try {

            $formation = Formation::where('id', '=', $request->formation_id)->first();
            $demandeur = auth()->user()->demandeur;
            $user = auth()->user();

            // enregistrement de la tentative de postulation
            //$this->tentativePostulation($demandeur->id, $formation->id);

            ///empeche de postuler plusieurs fois a une formation
            $associationoe = AssociationOE::where('demandeur_id', '=', $demandeur->id)->where('formation_id', '=', $formation->id)->first();

            if ($associationoe) {
                return response()->json(
                    [
                        "status" => "failed",
                        "message" => 'Vous avez déjà postulé à cette offre de formation MPPE!'
                    ],200);
            }
            // on postule quand toute les vérifications sont ok
           $message = $this->postuleFormation($demandeur, $formation,$request, "mppe",$user);

        } catch (\Exception $e) {
            Log("postulation offre de formation" . $e->getMessage());
            $message =   [
                "status" => "failed",
                "message" => "postulation offre de formation" . $e->getMessage()
            ];
        }

        return response()->json( $message,200);
    }

    // Permet d'enregistrer les nombres de tentatives de postulation.
    public function tentativePostulation($demandeurId, $formationId) {
        try {
            $tentativeOffreFormation = TentativeOffreFormationDemandeur::where('demandeur_id', $demandeurId)
                ->where('formation_id', $formationId)
                ->first();
            if ($tentativeOffreFormation == null) {
                $tentativeOffreFormation = New TentativeOffreFormationDemandeur();
                $tentativeOffreFormation->demandeur_id = $demandeurId;
                $tentativeOffreFormation->formation_id = $formationId;
                $tentativeOffreFormation->nombre_tentative = 1;
            } else {
                $tentativeOffreFormation->nombre_tentative += 1;
            }
            $tentativeOffreFormation->save();
        } catch (\Exception $e) {
            Log("Une erreur est survenue lors de sauvegarde de la tentative de postulation, Raison : " . $e->getMessage());
            return response()->json(
                [
                    "status" => "failed",
                    "message" => "Une erreur est survenue lors de sauvegarde de la tentative de postulation, Raison : " . $e->getMessage()
                ],200);
        }
    }

    public function onPostuler(Request $request,$formation_id){

        try {
            //we check if applicant has given reason
            if (strlen($request->motif) <= 0) {
                return response()->json(["status" => "failed", "message" => "Veuillez renseignez le motif de la formation"],401);
            }

            $formation = Formation::where('id', '=', $formation_id)->first();

            $demandeur = Auth::user()->demandeur;

            $user = Auth::user();

            //si le demandeur n'a pas renseigné son profil on lui demande de renseigner son profil
            if ($demandeur->secteuractivite_id == 0) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Veuillez renseignez votre profil demandeur"
                ],401);
            }

            $associationoe = AssociationOE::where('demandeur_id', '=', $demandeur->id)->where('formation_id', '=', $formation->id)->first();
            if ($associationoe) {
                return response()->json([
                    "status" => "failed",
                    "message" => "Vous avez déjà postuler à cette offre de formation !"
                ],401);
            }

            if($formation->typeformation->categorieformation_id == 1){

                $message = $this->postuleFormation($demandeur, $formation, $request->all(),'fcq',$user);
            } else {

                $message = $this->postuleFormation($demandeur, $formation, $request->all(),'mppe',$user);
            }

        } catch (\Exception $e) {

            Log("Formation Postulation: ". $e->getMessage());
            $message = [
                "status" => "failed",
                "message" => "une erreur s'est produite lors de l'enregistrement d'une formation !"
            ];
        }

        return response()->json($message,200);
    }

    public function postuleFormation($demandeur, $formation, Request $request, $type_formation,$user){

        try {

            $data = $request->all();
            $asso = New AssociationOE();
            $asso->demandeur_id = $demandeur->id;
            $asso->formation_id = $formation->id;

            if ($type_formation == "mppe"){
                $asso->localite_id = $request->localite;
            }

            if (request()->has('motif')) {
                //formation fcq
                $asso->motivation = $request->motif;

            } else {
                //formation passeport
                if (request()->has('cni')){
                    // Obtenir le fichier image
                    $file = $request->file('cni');
                    // Créer un nom d'image basé sur le nom de l'utilisateur et l'horodatage actuel.
                    $filename = 'cni-'.$demandeur->matriculeaej.'-'. $formation->id.'.' . $file->getClientOriginalExtension();

                    if(Storage::disk('formationcni')->put($filename,  File::get($file))) {
                        $asso->cni = $filename;
                    }
                }

                if (request()->has('diplome')){
                    // Obtenir le fichier image
                    $file = $request->file('cni');
                    // Créer un nom d'image basé sur le nom de l'utilisateur et l'horodatage actuel.
                    $filename = 'diplome-'.$demandeur->matriculeaej.'-'. $formation->id.'.' . $file->getClientOriginalExtension();

                    if(Storage::disk('formationdiplome')->put($filename,  File::get($file))) {
                        $asso->diplome = $filename;
                    }
                }

                if ($data['travaille'] == 1) {
                    $asso->poste = $data['poste'];
                    $asso->domaine_activite_poste = $data['domaine_activite_travaille'];
                }

                if ($data['activite_generatrice_revenu'] == 1) {

                    $asso->domaine_activite_revenu = $data['domaine_activite_revenu'];
                }
            }

            $asso->save();

            $message =  [
                'status'    => "success",
                'message'   => 'Votre demande a été prise en compte !'
            ];

            //on vérifie que le user existe
            if ($user) {
                $nomprenomdemandeur = Auth::user()->demandeur->nom .' '. Auth::user()->demandeur->prenoms;
                Mail::to(Auth::user()->email)->send(new MailOffreEmploiPostulation($nomprenomdemandeur));
            }

            return $message;

        } catch (\Exception $ex) {
            $message =  [
                "status" => "failed",
                "message" => "Function postule Formation" . $ex->getMessage()
            ];
            return $message;
        }

    }

}
