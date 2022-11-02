<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use App\Jobs\AfterSaveProjetFinancement;
use App\Models\AgenceRegionale;
use App\Models\Commune;
use App\Models\District;
use App\Models\FormeJuridique;
use App\Models\ProjetFinancement;
use App\Models\Region;
use App\Models\SecteurActivite;
use App\Models\StatutProjet;
use App\Models\TypeProgramme;
use App\Models\TypeProjet;
use App\Models\Ville;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProjetFinancementController extends Controller
{

    public function loadDataParametre(){
        $secteuractivites = SecteurActivite::orderBy('libelle', 'ASC')->get();
        $formejuridiques  = FormeJuridique::orderBy('libelle', 'ASC')->get();
        $regions          = Region::orderBy('nom', 'ASC')->get();
        $villes           = Ville::orderBy('nom', 'ASC')->get();
        $communes         = Commune::orderBy('nom', 'ASC')->get();
        $divisions        = AgenceRegionale::orderBy('nom', 'ASC')->get();
        $typeprojets      = TypeProjet::where('deleted_at',null)->orderBy('libelle', 'ASC')->get();
        $typeprogrammes   = TypeProgramme::orderBy('libelle', 'ASC')->get();
        $districts        = District::orderBy('nom', 'ASC')->get();
        $statuts          = StatutProjet::orderBy('libelle', 'ASC')->get();

        return response()->json([
            'parameter' => [
                'secteuractivites'  =>   $secteuractivites,
                'formejuridiques'   =>   $formejuridiques,
                'regions'           =>   $regions,
                'villes'            =>   $villes,
                'communes'          =>   $communes,
                'divisions'         =>   $divisions,
                'typeprojets'       =>   $typeprojets,
                'typeprogrammes'    =>   $typeprogrammes,
                'districts'         =>   $districts,
                'statuts'           =>   $statuts,
            ]
        ]);
    }

    public function creerProjet(Request $request){

        $data = $request->all();

        try {

            $projet = ProjetFinancement::create($data);

            $users = DB::table('backend_users')
                ->where('role_id',7)
                ->where('divisionregionaleaej_id',$request->divisionregionaleaej_id)
                ->first();

            $demandeur    =   auth()->user()->demandeur;

            if (request()->has('planaffaire')){
                // Obtenir le fichier "planaffaire"
                $file = $request->file('planaffaire');
                // Créer un nom d'image basé sur le nom de l'utilisateur et l'horodatage actuel.
                $filename = 'planaffaireprojet-'.time().'.' . $file->getClientOriginalExtension();

                if(Storage::disk('planaffaireprojet')->put($filename,  File::get($file))) {

                    $projet->update([
                        'planaffaire'       => $filename,
                        'demandeur_id'      => $demandeur->id,
                        'matriculeprojet'   => 0,
                        'titreprojet'       => 0,
                        'statut_id'         => 2,
                        'status_projet'     => 1,
                        'traitementaeffectuer_id'     => 1,
                        'user_affect'       => [$users->id],
                    ]);

                }
            }

            AfterSaveProjetFinancement::dispatch($projet);

            $message =  [
                'status'    => "success",
                'message'   => 'Votre projet bien été prise en compte !'
            ];

        } catch (\Exception $e){
            $message =  [
                'status'   => "failed",
                'message'   => $e->getMessage()
            ];
        }

        return response()->json($message,200);
    }
}
