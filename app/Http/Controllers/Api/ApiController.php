<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog\Post;
use App\Models\Commune;
use App\Models\Formation;
use App\Models\OffreEmploi;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function  emploi_jeune_tv(){

        $apikey= 'AIzaSyAxB5YNDkk0ixwDid9DnU7FStbHEhoqZyI';
        $Channel_ID = 'UCOnoRr03TyplBjXPKY_vEDQ';
        $Max_Results = 10;

        $data = [];

        // Get videos from channel by YouTube Data API
        $apiData = @file_get_contents('https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId='.$Channel_ID.'&maxResults='.$Max_Results.'&key='.$apikey.'');

        if($apiData){
            $videoList = json_decode($apiData);

            foreach ($videoList->items as $item) {

                $data[]=[
                    'v'             => $item->id->videoId,
                    'title'         => $item->snippet->title,
                    'description'   => $item->snippet->description,
                    'image'         => $item->snippet->thumbnails->medium->url,
                    'embed'         => 'https://www.youtube.com/embed/'.$item->id->videoId,
                ];
            }

        }else{
            echo 'Invalid API key or channel ID.';
        }

        return response()->json($data);
    }

    public function demandeur_info($matricule,$token = null){
        $search = \request('matricule');
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9";

        if($search == ''){
            $demandeurs = DB::table('digit_demandeur_demandeuremploi')->orderby('matriculeaej','asc')
                ->leftjoin('digit_parametrage_niveauetude',
                    'digit_parametrage_niveauetude.id', '=', 'digit_demandeur_demandeuremploi.niveauetude_id')
                ->leftjoin('digit_parametrage_diplome',
                    'digit_parametrage_diplome.id', '=', 'digit_demandeur_demandeuremploi.diplome_id')
                ->leftJoin('digit_parametrage_ville as domicile',
                    'domicile.id', '=', 'digit_demandeur_demandeuremploi.villeresidence_id')
                ->leftJoin('digit_parametrage_ville as lieu_naissance',
                    'lieu_naissance.id', '=', 'digit_demandeur_demandeuremploi.lieunaissance_id')
                ->leftJoin('digit_parametrage_sexe as sexe',
                    'sexe.id', '=', 'digit_demandeur_demandeuremploi.sexe_id')
                ->leftJoin('digit_parametrage_typepieceidentite as typepieceidentite',
                    'typepieceidentite.id', '=', 'digit_demandeur_demandeuremploi.typepieceidentite_id')
                ->leftJoin('digit_parametrage_commune as commune',
                    'commune.id', '=', 'digit_demandeur_demandeuremploi.lieuhabitation_id')
                ->leftJoin('digit_parametrage_specialite as specialite',
                    'specialite.id','=','digit_demandeur_demandeuremploi.specialite_id')
                ->select(
                    'digit_demandeur_demandeuremploi.*',
                    'commune.nom as lieuhabitation',
                    'typepieceidentite.libelle as typepieceidentite',
                    'specialite.libelle as specialite',
                    'sexe.libelle as sexe',
                    'lieu_naissance.nom as lieunaissance',
                    'domicile.nom as ville',
                    'digit_parametrage_niveauetude.libelle as niveauetude',
                    'digit_parametrage_diplome.libelle as diplome')
                ->limit(10)->get();

        }else{

            $demandeurs = DB::table('digit_demandeur_demandeuremploi')->orderby('matriculeaej','asc')
                ->leftjoin(
                    'digit_parametrage_niveauetude',
                    'digit_parametrage_niveauetude.id', '=', 'digit_demandeur_demandeuremploi.niveauetude_id')
                ->leftjoin('digit_parametrage_diplome',
                    'digit_parametrage_diplome.id', '=', 'digit_demandeur_demandeuremploi.diplome_id')
                ->leftJoin('digit_parametrage_ville as domicile',
                    'domicile.id', '=', 'digit_demandeur_demandeuremploi.villeresidence_id')
                ->leftJoin('digit_parametrage_ville as lieu_naissance',
                    'lieu_naissance.id', '=', 'digit_demandeur_demandeuremploi.lieunaissance_id')
                ->leftJoin('digit_parametrage_sexe as sexe', 'sexe.id', '=', 'digit_demandeur_demandeuremploi.sexe_id')
                ->leftJoin('digit_parametrage_typepieceidentite as typepieceidentite',
                    'typepieceidentite.id', '=', 'digit_demandeur_demandeuremploi.typepieceidentite_id')
                ->leftJoin('digit_parametrage_commune as commune',
                    'commune.id', '=', 'digit_demandeur_demandeuremploi.lieuhabitation_id')
                ->leftJoin('digit_parametrage_specialite as specialite','specialite.id','=','digit_demandeur_demandeuremploi.specialite_id')
                ->select(
                    'digit_demandeur_demandeuremploi.*',
                    'commune.nom as lieuhabitation',
                    'typepieceidentite.libelle as typepieceidentite',
                    'sexe.libelle as sexe',
                    'lieu_naissance.nom as lieunaissance',
                    'domicile.nom as ville',
                    'specialite.libelle as specialite',
                    'digit_parametrage_niveauetude.libelle as niveauetude',
                    'digit_parametrage_diplome.libelle as diplome')
                ->where('matriculeaej', 'like', '%' .$search . '%')->limit(10)->get();
        }

        $response = array();

        foreach($demandeurs as $demandeur){

            $response[] = array(
                "value"             => $demandeur->id,
                "lieuhabitation"    => $demandeur->lieuhabitation,
                "label"             => strtoupper($demandeur->matriculeaej),
                "sexe"              => strtoupper($demandeur->sexe),
                "matriculeaej"      => strtoupper($demandeur->matriculeaej),
                'nom'               => strtoupper($demandeur->nom),
                "prenom"            => strtoupper($demandeur->prenoms),
                "lieu_naissance"    => strtoupper($demandeur->lieunaissance),
                "domicile"          => strtoupper($demandeur->ville),
                "dateinscription"   => $demandeur->dateinscription,
                "datenaissance"     => $demandeur->datenaissance,
                "typepieceidentite" => strtoupper($demandeur->typepieceidentite),
                "numerocni"         => strtoupper($demandeur->numerocni),
                "age"               => Carbon::parse($demandeur->datenaissance)->age,
                "telephone"         => $demandeur->telephone,
                "niveauetude"       => strtoupper($demandeur->niveauetude),
                "diplome"           => strtoupper($demandeur->diplome),
                "specialite"        => strtoupper($demandeur->specialite),
                "nationalite"       => strtoupper(($demandeur->paysnationalite_id)?'IVOIRIENNE':'NON IVOIRIENNE'),
            );
        }

        if(request('token') == $token){
            return response()->json($response);
        }else{
            return response()->json("pas autorise");
        }
    }

    public function offre_emplois_bcpe(){

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
                'nbrjour'                   => $interval->days
            ];
        }

        return response()->json($data);
    }

    public function list_offre_emplois(){
        $data = [];
        $query = OffreEmploi::getQuery();
        $offreemplois = $query->join('digit_parametrage_diplome', 'digit_parametrage_diplome.id', '=', 'digit_offreformation_offreemploi.diplome_id')
            ->join('digit_parametrage_typecontrat', 'digit_parametrage_typecontrat.id', '=', 'digit_offreformation_offreemploi.typecontrat_id')
            ->whereDate('datefinoffre', '>=', date('Y-m-d'))
            ->whereNotNull('digit_offreformation_offreemploi.datepublication')
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
                'lieudetravail'             => Str::limit($item->lieudetravail, 10, $end = '...'),
                'noreference'               => $item->noreference,
                'descriptiontachesposte'    => Str::limit(strip_tags($item->descriptiontachesposte), 200, $end = '...'),
                'datedebutoffre'            => Carbon::parse($item->datepublication)->format('d M Y'),
                'datefinoffre'              => Carbon::parse($item->datefinoffre)->format('d M Y'),
                'created_at'                => Carbon::parse($item->datepublication)->format('d M Y'),
                'nombreposte'               => $nbrepost,
                'typecontrat_libelle'       => $item->libelletypecontrat,
                'typecontrat_codecolor'     => $item->codecouleur,
                'nbrjour'                   => $interval->days
            ];
        }

        return response()->json($data);
    }

    public function list_offre_formation(){

        $query = Formation::getQuery();
        $offreformations = $query->join('digit_parametrage_diplome',
            'digit_parametrage_diplome.id', '=', 'digit_offreformation_formation.diplome_id')
            ->join('digit_parametrage_typeformation',
                'digit_parametrage_typeformation.id', '=', 'digit_offreformation_formation.typeformation_id')
            ->join('digit_parametrage_categorieformation',
                'digit_parametrage_categorieformation.id', '=', 'digit_offreformation_formation.categorieformation_id')
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
            ->select('digit_offreformation_formation.*',
                'digit_parametrage_diplome.libelle as diplomelibelle',
                'digit_parametrage_categorieformation.id as categorie_id',
                'digit_parametrage_typeformation.libelle  as typeformation')
            ->get();

        $data = [];

        foreach($offreformations as $item){

            $date1 = new DateTime($item->datedebut);
            $date2 = new DateTime(now());
            $interval = $date1->diff($date2);

            if($item->categorie_id == 1){
                $categorie = "Formations FCQ";
            }else{
                $categorie = "Formations MPPE";
            }

            $data[] =[
                'id'                        => $item->id,
                'intitule'                  => \Illuminate\Support\Str::limit($item->intitule, 30, $end = '...'),
                'lieudetravail'             => \Illuminate\Support\Str::limit($item->lieu, 10, $end = '...'),
                'noreference'               => \Illuminate\Support\Str::limit(strip_tags($item->reference), 15, $end = '...'),
                'descriptiontachesposte'    =>  \Illuminate\Support\Str::limit(strip_tags($item->description), 90, $end = '...'),
                'datedebutoffre'            => \Carbon\Carbon::parse($item->datedebut)->format('d M Y'),
                'datefinoffre'              => \Carbon\Carbon::parse($item->datefin)->format('d M Y'),
                'created_at'                => \Carbon\Carbon::parse($item->created_at)->format('d M Y'),
                'categorieformation_id'     => $item->categorieformation_id,
                'typeformation_id'          => $item->typeformation_id,
                'typecontrat_libelle'       => $categorie,
                'typecontrat_codecolor'     => "green",
                'nbrjour'                   => $interval->days
            ];
        }

        return response()->json($data);
    }

    public function campagne_cour(){
        $slideshows= Post::with('featured_images')->whereHas('categories',function($q){
            $q->whereId(21);
        })->where('published',1)->orderBy('id','desc')->get();
        return response()->json($slideshows);
    }

    public function list_slideshow(){

        $slideshows=Post::with('featured_images')->whereHas('categories',function($q){
            $q->whereName('Slide show');
        })->where('published',1)->orderBy('id','desc')->get();
        return response()->json($slideshows);
    }

    public function actualites_slideshow(){
        $actualites= Post::with('featured_images')->whereHas('categories', function($q){
            $q->whereId(2);
        })->where('published',1)->orderBy('id','desc')->get();
        return response()->json($actualites);
    }

    public function autocompleteCommune(Request $request){
        $search = $request->search;
        if($search == ''){
            $communes = Commune::orderBy('nom', 'ASC')->select('id','nom')->limit(10)->get();
        }else{
            $communes = Commune::orderBy('nom', 'ASC')->select('id','nom')->where('nom', 'like', '%' .$search . '%')->limit(10)->get();
        }
        $response = array();
        foreach($communes as $commune){
            $response[] = array("value"=> $commune->id,"label"=> $commune->nom);
        }
        return response()->json($response);
    }


}
