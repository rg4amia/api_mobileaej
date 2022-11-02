<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use App\Models\AgenceRegionale;
use App\Models\Diplome;
use App\Models\FavorisOffreEmploi;
use App\Models\NiveauEtude;
use App\Models\SecteurActivite;
use App\Models\Specialite;
use App\Models\SpecialiteDiplome;
use App\Models\TypeContrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MesFavorisController extends Controller
{

    public function index(){

        $favori         = FavorisOffreEmploi::where('user_id',auth()->user()->id)->latest('id')->first();
        if($favori){
            $typecontrat    = TypeContrat::whereIn('id', $favori->typecontrat)->get();
            $niveauetude    = NiveauEtude::whereIn('id', $favori->niveauetude)->get();
            $specialite     = SecteurActivite::whereIn('id', $favori->specialite)->get();
            $diplome        = Diplome::whereIn('id', $favori->diplome)->get();
            $agenceregionale        = AgenceRegionale::whereIn('id', $favori->agenceregionale)->get();

            $response = [
                'data'      => [
                    'typecontrat'       => $typecontrat,
                    'niveauetude'       => $niveauetude,
                    'specialite'        => $specialite,
                    'diplome'           => $diplome,
                    'agenceregionale'   => $agenceregionale,
                ],
                'favoris'   => $favori
            ];
            return response()->json($response,200);

        } else{
            $response = ["status" => "failed", "message" => 'Pas de donnée enregistrée !!!',];
            return response()->json($response,401);
        }
    }

    public function store(Request $request){

        $data = $request->all();

        try {

            $data['user_id'] =  auth()->user()->id;
            $data['demandeur_id'] = auth()->user()->demandeur->id;

            if($request->has('niveauetude')){
                $data['niveauetude'] = $request->niveauetude;
            }

            if($request->has('typecontrat')){
                $data['typecontrat'] = $request->typecontrat;
            }

            if($request->has('agenceregionale')){
                $data['agenceregionale'] = $request->agenceregionale;
            }

            if($request->has('specialite')){
                $data['specialite'] = $request->specialite;
            }

            if($request->has('diplome')){
                $data['diplome'] = $request->diplome;
            }

            $favoris = FavorisOffreEmploi::create($data);

            foreach ($request->specialite as $item) {
                $favoris->specialite()->attach($item);
            }

            $message = [
                "status"    => "success",
                "message"   => "favoris bien ajoute"
            ];

        } catch (\Exception $exception){

            $message = ["status" => "failed", "message" => $exception->getMessage()];
        }

        return response()->json($message,200);
    }
}
