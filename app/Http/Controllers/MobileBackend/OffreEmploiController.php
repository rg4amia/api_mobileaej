<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use App\Mail\MailOffreEmploiPostulation;
use App\Models\AgenceRegionale;
use App\Models\DemandeurEmploi;
use App\Models\Diplome;
use App\Models\NiveauEtude;
use App\Models\OffreEmploi;
use App\Models\SecteurActivite;
use App\Models\TypeContrat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use DateTime;
use function Symfony\Component\String\upper;
use App\Models\OffreEmploiDemandeur as AssociationOE;

class OffreEmploiController extends Controller
{
    const PUBLIER = 1;
    const STATUT_OFFRE = 1;
    const TYPE_OFFRE_BCPE = 6;

    CONST REFUSE_DEMANDE = 0; //la demande est refusée
    CONST VALIDE_DEMANDE = 1; //la demande est valider
    CONST DEMANDE_ACCEPTER_REFUSER = 2; //la demande est passée mais elle est refusée

    public function specialiteOffreEmploi(){

        $typecontrats = TypeContrat::all();
        $diplomes = Diplome::all();
        $specialites = SecteurActivite::all();
        $niveauetude = NiveauEtude::all();
        $agenceregionale = AgenceRegionale::orderBy('nom','ASC')->get();
        $data_diplome = array();

        foreach ($diplomes as $diplome) {
            $pieces = explode(" (", $diplome->libelle);
            $data_diplome []=[
                'id' => $diplome->id,
                'libelle' => $pieces[0]
            ];
        }

        return response()->json(['parameter' => [
            'specialite'        => $specialites,
            'typecontrat'       => $typecontrats,
            'diplome'           => $data_diplome,
            'niveauetude'       => $niveauetude,
            'agenceregionale'   => $agenceregionale,
        ]],200);
    }

    /*public function specialiteOffreEmploi(){

        $data = [];

        $offreemplois = OffreEmploi::with('typecontrat','diplome','sexe','specialites')
            ->join('digit_parametrage_diplome', 'digit_parametrage_diplome.id', '=', 'digit_offreformation_offreemploi.diplome_id')
            ->join('digit_parametrage_typecontrat', 'digit_parametrage_typecontrat.id', '=', 'digit_offreformation_offreemploi.typecontrat_id')
            ->whereDate('datefinoffre', '>=', date('Y-m-d'))
            ->whereNotNull('digit_offreformation_offreemploi.datepublication')
            ->where('statutoffre_id', 1)
            ->whereNull('digit_offreformation_offreemploi.deleted_at')
            ->orderBy('digit_offreformation_offreemploi.updated_at', 'desc')
            ->groupBy('digit_offreformation_offreemploi.id')
            ->select('digit_offreformation_offreemploi.*', 'digit_parametrage_diplome.libelle as diplomelibelle', 'digit_parametrage_typecontrat.libelle as libelletypecontrat', 'digit_parametrage_typecontrat.codecouleur')
            ->get();

        $data_sp = array();
        $data_typ = array();
        $data_diplome = array();

        foreach ($offreemplois as $offreemploi){
                if($offreemploi->specialites->count() >0)
                    //$data [] =$offreemploi->specialites;
                    foreach ($offreemploi->specialites as $sp){

                        if(count($data_typ) > 100) {
                            if(!in_array($sp->id,$data_sp["id"])){
                                $data_sp [] =[
                                    'id'        => $sp->id,
                                    'libelle'   => $sp->libelle,
                                    'description'   => $sp->description,
                                    'pivot'         => $sp->pivot,
                                ];
                            }
                        } else {
                            $data_sp [] =[
                                'id'        => $sp->id,
                                'libelle'   => $sp->libelle,
                                'description'   => $sp->description,
                                'pivot'         => $sp->pivot,
                            ];
                        }
                    }

                if(count($data_typ) > 100) {
                   // dd($data_typ);
                    if(!in_array($offreemploi->typecontrat->id,$data_typ["id"])){
                        $data_typ [] = [
                            'id' =>$offreemploi->typecontrat->id,
                            'libelle' =>$offreemploi->typecontrat->libelle
                        ];
                    }

                } else{
                    $data_typ [] = [
                        'id' =>$offreemploi->typecontrat->id,
                        'libelle' =>$offreemploi->typecontrat->libelle
                    ];
                }

                if(count($data_diplome)> 100){
                    if(!in_array($offreemploi->diplome->id,$data_diplome["id"])){
                        $data_diplome[] = [
                            'id'        => $offreemploi->diplome->id,
                            'libelle'   => $offreemploi->diplome->libelle,
                        ];
                    }
                }else{
                    $data_diplome[] = [
                        'id'        => $offreemploi->diplome->id,
                        'libelle'   => $offreemploi->diplome->libelle,
                    ];
                }

                /*f($offreemploi->typecontrat->count() > 0) {
                     foreach ($offreemploi->typecontrat as $typ){
                         //dd($offreemploi->typecontrat);
                         $data_typ = $offreemploi->typecontrat;
                     }
                 }*/
       /* }*/

        //$collection = collect($data_sp);
        //$final = $collection->unique('id')->all();

  /*      return response()->json(['parameter' => [
            'specialite'    => $data_sp,
            'typecontrat'   => $data_typ,
            'diplome'       => $data_diplome,
        ]],200);
    }*/

    public function offreEmploisBcpe(){

        $data = [];
        $query = OffreEmploi::getQuery();
        $offreemplois = $query->join('digit_parametrage_diplome', 'digit_parametrage_diplome.id', '=', 'digit_offreformation_offreemploi.diplome_id')
            ->join('digit_parametrage_typecontrat', 'digit_parametrage_typecontrat.id', '=', 'digit_offreformation_offreemploi.typecontrat_id')
            // ->join('digit_parametrage_secteuractivite', 'digit_parametrage_secteuractivite.id', '=', 'digit_offreformation_offreemploi.secteuractivite_id')
            ->whereDate('datefinoffre', '>=', date('Y-m-d'))
            ->whereNotNull('digit_offreformation_offreemploi.datepublication')
            ->where('digit_offreformation_offreemploi.typeposteoffre_id','=',6)
            ->where('statutoffre_id', 1)
            ->whereNull('digit_offreformation_offreemploi.deleted_at')
            ->orderBy('digit_offreformation_offreemploi.updated_at', 'desc')
            ->groupBy('digit_offreformation_offreemploi.id')
            ->select('digit_offreformation_offreemploi.*', 'digit_parametrage_diplome.libelle as diplomelibelle', 'digit_parametrage_typecontrat.libelle as libelletypecontrat', 'digit_parametrage_typecontrat.codecouleur')
            ->get();

        $nbrepost = 0;

        foreach($offreemplois as $item){
            $nbrepost = $item->nombreposte + $nbrepost;
            $date1 = new DateTime($item->datepublication);
            $date2 = new DateTime(now());
            $interval = $date1->diff($date2);

            $data[] =[
                'id'                        => $item->id,
                'intitule'                  => $item->intitule,
                'lieudetravail'             => \Illuminate\Support\Str::limit($item->lieudetravail, 10, $end = '...'),
                'noreference'               => $item->noreference,
                'descriptiontachesposte'    =>  \Illuminate\Support\Str::limit(strip_tags($item->descriptiontachesposte), 200, $end = '...'),
                'datedebutoffre'            => \Carbon\Carbon::parse($item->datepublication)->format('d M Y'),
                'datefinoffre'              => \Carbon\Carbon::parse($item->datefinoffre)->format('d M Y'),
                'created_at'                => \Carbon\Carbon::parse($item->datepublication)->format('d M Y'),
                'nombreposte'               => $nbrepost,
                'typecontrat_libelle'       => $item->libelletypecontrat,
                'typecontrat_codecolor'     => $item->codecouleur,
                'nbrjour'                   => $interval->days,

            ];
        }

        return response()->json($data);
    }

    //public function listOffreEmplois($specialite_id = null,$diplome_id = null,$typecontrat_id = null){
    public function listOffreEmplois(Request $request){

        $data = [];
        $dataSpecialite = [];

        $offreemplois = OffreEmploi::with('typecontrat','diplome','sexe','specialites','niveauetude')
            ->join('digit_parametrage_diplome', 'digit_parametrage_diplome.id', '=', 'digit_offreformation_offreemploi.diplome_id')
            ->join('digit_parametrage_typecontrat', 'digit_parametrage_typecontrat.id', '=', 'digit_offreformation_offreemploi.typecontrat_id')
            ->whereDate('datefinoffre', '>=', date('Y-m-d'))
            ->whereNotNull('digit_offreformation_offreemploi.datepublication')
            ->where('statutoffre_id', 1)
            ->whereNull('digit_offreformation_offreemploi.deleted_at')
            ->orderBy('digit_offreformation_offreemploi.updated_at', 'desc')
            ->groupBy('digit_offreformation_offreemploi.id')
            ->select('digit_offreformation_offreemploi.*', 'digit_parametrage_diplome.libelle as diplomelibelle', 'digit_parametrage_typecontrat.libelle as libelletypecontrat', 'digit_parametrage_typecontrat.codecouleur');

        if($request->agenceregional_id){
            Log::info($request->agenceregional_id);

            $offreemplois->whereHas('agenceregionale', function ($q) use ($request) {
                $q->whereIn('id',$request->agenceregional_id);
            });
        }

        if($request->specialite_id){
            Log::info($request->specialite_id);

            $offreemplois->whereHas('specialites', function ($q) use ($request) {
                $q->whereIn('id',$request->specialite_id);
            });
        }

        if($request->diplome_id){
            Log::info($request->diplome_id);
            ///pions chaine de caractere en tableau
            /*  $chaineArray = str_replace("[","",$request->diplome_id);
            $chaineArray = str_replace("]","",$chaineArray);
            $choix  = explode(',', $chaineArray);

            foreach ($choix as $item) {
                $txt  = str_replace('"',"",$item);
                $diplomeArrayInteger[] = (int)$txt;
            }*/
            $offreemplois->whereHas('diplome', function ($q) use ($request) {
                $q->whereIn('id',$request->diplome_id);
            });
        }

        if($request->typecontrat_id){
            Log::info($request->typecontrat_id);
            $offreemplois->whereHas('typecontrat', function ($q) use ($request) {
                $q->whereIn('id',$request->typecontrat_id);
            });
        }

        $nbrepost = 0;

        foreach($offreemplois->get() as $key => $item){

            $nbrepost           = $item->nombreposte + $nbrepost;
            $date1              = new DateTime($item->datepublication);
            $date2              = new DateTime(now());
            $interval           = $date1->diff($date2);

            $datepublication    = \Carbon\Carbon::parse($item->datepublication)->translatedFormat('d M Y');
            $datefinoffre       = \Carbon\Carbon::parse($item->datefinoffre)->translatedFormat('d M Y');
            $created_at         = \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y');

            $associationoe = AssociationOE::where('demandeur_id', '=', Auth::user()->demandeur->id)->where('offreemploi_id', '=', $item->id)->first();

            $data[] =   [
                'id'                        => $item->id,
                'intitule'                  => $item->intitule,
                'lieudetravail'             => $item->lieudetravail,
                'noreference'               => $item->noreference,
                'descriptiontachesposte'    => $item->descriptiontachesposte,
                'datedebutoffre'            => strtoupper($datepublication),
                'datefinoffre'              => strtoupper($datefinoffre),
                'created_at'                => strtoupper($created_at),
                'nombreposte'               => $nbrepost,
                'typecontrat_libelle'       => $item->libelletypecontrat,
                'niveauetude'               => $item->niveauetude,
                'diplome'                   => $item->diplome->libelle,
                'sexe'                      => $item->sexe,
                'specialites'                => $item->specialites,
                'annee_experience_professionnelle' => $item->annee_experience_professionnelle,
                'typecontrat_codecolor'     => (string)$item->codecouleur,
                'nbrjour'                   => $interval->days,
                'entreprise_id'             => $item->entreprise_id ?: 0,
                'cabinetplacement_id'       => $item->cabinetplacement_id ?: 0,
                'postule'                   => $associationoe ? true : false,
                'dataSpecialite'            => $dataSpecialite,
            ];
        }

        return response()->json($data);
    }

    public function listOffreEmploisWithOutConnexion(Request $request){

        $data = [];
        $dataSpecialite = [];

        $offreemplois = OffreEmploi::with('typecontrat', 'diplome', 'sexe', 'specialites', 'niveauetude')
            ->join('digit_parametrage_diplome', 'digit_parametrage_diplome.id', '=', 'digit_offreformation_offreemploi.diplome_id')
            ->join('digit_parametrage_typecontrat', 'digit_parametrage_typecontrat.id', '=', 'digit_offreformation_offreemploi.typecontrat_id')
            ->whereDate('datefinoffre', '>=', date('Y-m-d'))
            ->whereNotNull('digit_offreformation_offreemploi.datepublication')
            ->where('statutoffre_id', 1)
            ->whereNull('digit_offreformation_offreemploi.deleted_at')
            ->orderBy('digit_offreformation_offreemploi.updated_at', 'desc')
            ->groupBy('digit_offreformation_offreemploi.id')
            ->select('digit_offreformation_offreemploi.*', 'digit_parametrage_diplome.libelle as diplomelibelle', 'digit_parametrage_typecontrat.libelle as libelletypecontrat', 'digit_parametrage_typecontrat.codecouleur');

        if ($request->agenceregional_id) {
            Log::info($request->agenceregional_id);

            $offreemplois->whereHas('agenceregionale', function ($q) use ($request) {
                $q->whereIn('id', $request->agenceregional_id);
            });
        }

        if ($request->specialite_id) {
            Log::info($request->specialite_id);

            $offreemplois->whereHas('specialites', function ($q) use ($request) {
                $q->whereIn('id', $request->specialite_id);
            });
        }

        if ($request->diplome_id) {
            Log::info($request->diplome_id);
            ///pions chaine de caractere en tableau
            /*  $chaineArray = str_replace("[","",$request->diplome_id);
            $chaineArray = str_replace("]","",$chaineArray);
            $choix  = explode(',', $chaineArray);

            foreach ($choix as $item) {
                $txt  = str_replace('"',"",$item);
                $diplomeArrayInteger[] = (int)$txt;
            }*/
            $offreemplois->whereHas('diplome', function ($q) use ($request) {
                $q->whereIn('id', $request->diplome_id);
            });
        }

        if ($request->typecontrat_id) {
            Log::info($request->typecontrat_id);
            $offreemplois->whereHas('typecontrat', function ($q) use ($request) {
                $q->whereIn('id', $request->typecontrat_id);
            });
        }

        $nbrepost = 0;

        foreach ($offreemplois->get() as $key => $item) {

            $nbrepost           = $item->nombreposte + $nbrepost;
            $date1              = new DateTime($item->datepublication);
            $date2              = new DateTime(now());
            $interval           = $date1->diff($date2);

            $datepublication    = \Carbon\Carbon::parse($item->datepublication)->translatedFormat('d M Y');
            $datefinoffre       = \Carbon\Carbon::parse($item->datefinoffre)->translatedFormat('d M Y');
            $created_at         = \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y');

            //$associationoe = AssociationOE::where('demandeur_id', '=', Auth::user()->demandeur->id)->where('offreemploi_id', '=', $item->id)->first();

            $data[] =   [
                'id'                        => $item->id,
                'intitule'                  => $item->intitule,
                'lieudetravail'             => $item->lieudetravail,
                'noreference'               => $item->noreference,
                'descriptiontachesposte'    => $item->descriptiontachesposte,
                'datedebutoffre'            => strtoupper($datepublication),
                'datefinoffre'              => strtoupper($datefinoffre),
                'created_at'                => strtoupper($created_at),
                'nombreposte'               => $nbrepost,
                'typecontrat_libelle'       => $item->libelletypecontrat,
                'niveauetude'               => $item->niveauetude,
                'diplome'                   => $item->diplome->libelle,
                'sexe'                      => $item->sexe,
                'specialites'                => $item->specialites,
                'annee_experience_professionnelle' => $item->annee_experience_professionnelle,
                'typecontrat_codecolor'     => (string)$item->codecouleur,
                'nbrjour'                   => $interval->days,
                'entreprise_id'             => $item->entreprise_id ?: 0,
                'cabinetplacement_id'       => $item->cabinetplacement_id ?: 0,
                //'postule'                   => $associationoe ? true : false,
                'dataSpecialite'            => $dataSpecialite,
            ];
        }

        return response()->json($data);
    }

    public function onEmploiPostule($offre_emploi_id) {

        try {

            $offreemplois = DB::table('digit_offreformation_offreemploi')
                ->leftjoin('digit_parametrage_niveauetude', 'digit_parametrage_niveauetude.id', '=', 'digit_offreformation_offreemploi.niveauetude_id')
                ->whereNotNull('digit_offreformation_offreemploi.datepublication')
                ->whereNull('digit_offreformation_offreemploi.deleted_at')
                ->where('digit_offreformation_offreemploi.id', '=', $offre_emploi_id)
                ->whereDate('digit_offreformation_offreemploi.datefinoffre', '>=', date('Y-m-d'))
                ->select('digit_offreformation_offreemploi.*', 'digit_parametrage_niveauetude.old as old')
                ->first();

            $demandeur = Auth::user()->demandeur;

            //dd($demandeur->niveauetude->old);

            //on vérifie si le demandeur a déja postuler a cette offre
            $associationoe = AssociationOE::where('demandeur_id', '=', $demandeur->id)->where('offreemploi_id', '=', $offreemplois->id)->first();

            if ($associationoe) {
                //s'il a déja postuler on l'envoie un message pour lui indiquer qu'il a déja postuler
                return response()->json(["status" => "failed", "message" => "Vous avez déja postulé à cette offre d'emploi !"],200);
            } else {
                //enregistre l'etat du postule
                $valide = self::VALIDE_DEMANDE;

                if($offreemplois->id != 18074){
                    //on vérifie si le demandeur a renseigné son niveau d'étude
                    if (!$demandeur->niveauetude_id) {
                        //on met l'état de la validation a 0
                        $valide = self::REFUSE_DEMANDE;
                       return response()->json(["status" => "failed", "message" => "Veuillez renseignez votre niveau d'étude dans votre profil."],200);
                    }

                    // on vérifie si le demandeur a renseigné son secteur d'activité
                    if (!$demandeur->secteuractivite_id) {
                        $valide = self::REFUSE_DEMANDE;
                       return response()->json(["status" => "failed", "message" => "Veuillez renseignez votre secteur d'activité dans votre profil."],200);
                    }

                    // recuperation de l'offre sous forme de model
                    $offreEmplois = OffreEmploi::find($offreemplois->id);

                    // On vérifie si la spécialité(secteur d'activite) du demandeur correspond à une spécialité de l'offre(secteur d'activite)
                    //dd( $demandeur->secteuractivite_id, $offreemplois->secteuractivite_id);

                   /* if (count($offreEmplois->specialites->where('id', $demandeur->secteuractivite_id)) == 0) {
                        if ($demandeur->secteuractivite_id != $offreemplois->secteuractivite_id) {
                            //on met l'état de la validation a 2
                            $valide = self::DEMANDE_ACCEPTER_REFUSER;
                            return response()->json(["status" => "failed", "message" => "Vous n'avez pas la spécialité requise pour cette offre"],200);
                        }
                    }*/

                    // on vérifie que le niveau d'étude du demandeur est supérieur ou égal au niveau d'étude de l'offre d'emplois
                    if ($offreemplois->old > $demandeur->niveauetude->old) {
                        //si le niveau d'étude du demandeur est inférieur a celui de l'offre on le met a 2
                        $valide = self::DEMANDE_ACCEPTER_REFUSER;
                        //return response()->json(["status" => "failed", "message" => "Votre niveau d'études ne correspond pas à l'offre."],200);
                    }
                }

                if ($valide) {
                    $asso = New AssociationOE();
                    $asso->demandeur_id = $demandeur->id;
                    $asso->offreemploi_id = $offreemplois->id;

                    if ($valide == self::DEMANDE_ACCEPTER_REFUSER || $valide == self::REFUSE_DEMANDE) {
                        $asso->estpreselectionne = 0;
                        $asso->estretenu = 0;
                        $asso->estrefuser = 1;
                    } elseif ($valide == self::VALIDE_DEMANDE) {
                        $asso->estrefuser = 0;
                        $asso->estretenu = 0;
                        $asso->estpreselectionne = 1;
                    }

                    $asso->save();

                    $nomprenomdemandeur = Auth::user()->demandeur->nom .' '. Auth::user()->demandeur->prenoms;

                    Mail::to(Auth::user()->email)->send(new MailOffreEmploiPostulation($nomprenomdemandeur));

                    if (Mail::failures()) {
                        Log::warning( 'Mail: Désolé ! Veuillez réessayer ce dernier' );
                    }

                    if($valide == self::VALIDE_DEMANDE || $valide == self::DEMANDE_ACCEPTER_REFUSER){

                        $contenu    = 'Votre demande a été prise en compte ! Merci.';
                        $titre      = 'Postulation a l\'offre '. $offreemplois->noreference;

                        sendByUser(Auth::user()->demandeur->onesignale_id, $contenu,$titre);

                        /* $client = new \GuzzleHttp\Client(); */
                        //$data = \request('id');
                        //dd($data);    "include_player_ids":["'. $data.'"],

                       /*  $response = $client->request('POST', 'https://onesignal.com/api/v1/notifications', [
                            'body' => '{
                            "app_id" : "84bfcfa5-f200-461d-99af-f46c1de78bf7",
                             "included_segments":["Subscribed Users"],
                             "contents":{"en":"'. $contenu .'"},
                             "name":"INTERNAL_CAMPAIGN_NAME",
                             "headings": {"en":"'. $titre .'"}
                             }',
                            'headers' => [
                                'Accept' => 'application/json',
                                'Authorization' => 'Basic OTNmZTczZGMtYjYzNi00NDM3LTg3NmMtZTY0NDM1Y2JkMjA5',
                                'Content-Type' => 'application/json',
                            ],
                            ]); */
                        return response()->json(["status" => "success", "message" => "Votre demande a été prise en compte !"],200);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Une erreur est survenue dans le processus de postulation a une offre d'emplois, error => {$e->getMessage()}" );
            return response()->json(["status" => "failed", "message" => $e->getMessage()],200);

        }
    }
}
