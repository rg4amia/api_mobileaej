<?php

namespace App\Repositories;

use App\Models\DemandeurEmploi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

use Carbon\Carbon;

use App\Models\User;
use App\Models\EntreprisePrestataire;



class OffreEmploisRepository
{

	
     public function create_offre_entreprise_prestataire($request)
    {
//dd($request);
                $agence = $request->get('agence');
                $nom_entreprise = $request->get('nom_entreprise');
                $contribuable = $request->get('contribuable');
                $cnps = $request->get('cnps');
                $tel_entreprise = $request->get('tel_entreprise');
                $email_entreprise = $request->get('email_entreprise');
                $site_web = $request->get('site_web');
                $branche = $request->get('branche');
                $activite = $request->get('activite');
                $sit_geo = $request->get('sit_geo');
                $nom_resp = $request->get('nom_resp');
                $numero_resp = $request->get('numero_resp');
                $fonction_resp = $request->get('fonction_resp');
                $first_user = Auth::id();
                $actif = 1;


        try {

                $entrepriseprestataire = new EntreprisePrestataire();
                $entrepriseprestataire->divisionregionaleaej_id = $agence;
                $entrepriseprestataire->nomstructureoffre  = $nom_entreprise;
                $entrepriseprestataire->numcomptecontribuable = $contribuable;
                $entrepriseprestataire->numcnps = $cnps;
                $entrepriseprestataire->telentreprise = $tel_entreprise;
                $entrepriseprestataire->emailentreprise = $email_entreprise;
                $entrepriseprestataire->siteweb = $site_web;
                $entrepriseprestataire->branchesactivites = $branche;
                $entrepriseprestataire->principalesactivites= $activite;
                $entrepriseprestataire->situationgeographique= $sit_geo;
                $entrepriseprestataire->personneacontacter= $nom_resp;
                $entrepriseprestataire->telpersonnecontacter = $numero_resp;
                $entrepriseprestataire->fonction = $fonction_resp;
                $entrepriseprestataire->first_user = $first_user;
                $entrepriseprestataire->actif = $actif;
        

               $entrepriseprestataire->save();
            
               Session::flash('success',"Entreprise ajoutée avec succès");

            } catch (\Exception $exception){
                DB::rollback();
                    Session::flash('error',"Une erreur s'est produite ".$e->getMessage().", Réessayer svp");
                    return Redirect::back();
            }


    }


       public function edit_offre_entreprise_prestataire($request)
    {
//dd($request);

                $agence = $request->get('agence');
                $nom_entreprise = $request->get('nom_entreprise');
                $contribuable = $request->get('contribuable');
                $cnps = $request->get('cnps');
                $tel_entreprise = $request->get('tel_entreprise');
                $email_entreprise = $request->get('email_entreprise');
                $site_web = $request->get('site_web');
                $branche = $request->get('branche');
                $activite = $request->get('activite');
                $sit_geo = $request->get('sit_geo');
                $nom_resp = $request->get('nom_resp');
                $numero_resp = $request->get('numero_resp');
                $fonction_resp = $request->get('fonction_resp');
                $entreprise_id = $request->get('entreprise_id');


        try {


        $affected = DB::table('digit_offreformation_entrepriseprestataire')
              ->where('id', $entreprise_id)
              ->update([
                'divisionregionaleaej_id' => $agence,
                'nomstructureoffre'  => $nom_entreprise,
                'numcomptecontribuable' => $contribuable,
                'numcnps' => $cnps,
                'telentreprise' => $tel_entreprise,
                'emailentreprise' => $email_entreprise,
                'siteweb' => $site_web,
                'branchesactivites' => $branche,
                'principalesactivites' => $activite,
                'situationgeographique' => $sit_geo,
                'personneacontacter' => $nom_resp,
                'telpersonnecontacter' => $numero_resp,
                'fonction' => $fonction_resp,
                  ]);
            
               Session::flash('success',"Entreprise modifié avec succès");

            } catch (\Exception $exception){
                DB::rollback();
                    Session::flash('error',"Une erreur s'est produite ".$e->getMessage().", Réessayer svp");
                    return Redirect::back();
            }


    }

    

    




}


