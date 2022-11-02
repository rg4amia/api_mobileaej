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



class BackOfficeRepository
{

	 public function getBackendUserByMail($email)
     {
        $user = DB::table('backend_users')
               ->where('backend_users.email',$email)
               ->orWhere('backend_users.login',$email)
               ->first();

        return $user;
    }

    public function getBackendPermissionById($id)
    {
        $user = DB::table('backend_user_roles')
               ->join('backend_users','backend_user_roles.id','backend_users.role_id')
               ->select('backend_users.first_name','backend_users.last_name','backend_users.email','backend_users.telephonebureau','backend_users.last_login','backend_users.last_activity','backend_users.photo','backend_user_roles.name','backend_user_roles.code','backend_user_roles.description','backend_user_roles.permissions')
               ->where('backend_users.id',$id)
               ->first();

        return $user;
    }

    public function getApplicantjson()
    {

        $applicants = Cache::remember('users', 60*60*24, function () {
            return DB::table('users')
                ->join('digit_demandeur_demandeuremploi','users.demandeuremploi_id','digit_demandeur_demandeuremploi.id')
                ->select('users.id','users.name','users.email','users.last_login','users.cellulaire','users.entreprise_id','digit_demandeur_demandeuremploi.id',
                    'digit_demandeur_demandeuremploi.prenoms','digit_demandeur_demandeuremploi.matriculeaej','digit_demandeur_demandeuremploi.datenaissance',
                    'digit_demandeur_demandeuremploi.lieunaissance','digit_demandeur_demandeuremploi.lieuhabitation','digit_demandeur_demandeuremploi.ancienphoto')
                ->paginate(10); // By Using DB
        });

        return response()->json($applicants);

    }

    public function getApplicantById($id)
    {
        $user = DB::table('digit_demandeur_demandeuremploi')
               ->join('users','digit_demandeur_demandeuremploi.id','users.demandeuremploi_id')
               ->where('digit_demandeur_demandeuremploi.id',$id)
               ->first();

        return $user;
    }

    public function editGeneraleDataById($request)
    {
        try {

            $affected = DB::table('users')
              ->where('id', $request->user_id)
              ->update([
                       'name' => $request->nom,
                       'email' => $request->email,
                       'cellulaire' => $request->telephone,
                   ]);

        $affected_ = DB::table('digit_demandeur_demandeuremploi')
              ->where('id', $request->demandeur_id)
              ->update([
                       'nom' => $request->nom,
                       'prenoms' => $request->prenom,
                       'email' => $request->email,
                       'statudemandeur_id' => $request->statut_id,
                       'telephone' => $request->telephone
                   ]);
               Session::flash('success',"Informations du demandeur modifiées avec succès");

        } catch (\Exception $exception){
            DB::rollback();
                Session::flash('error',"Une erreur s'est produite ".$e->getMessage().", Réessayer svp");
                return Redirect::back();
        }


    }

    public function editDetailDataById($request)
    {

        try {

        $affected = DB::table('digit_demandeur_demandeuremploi')
              ->where('id', $request->demandeur_id)
              ->update([
                       
                       'dup_competence' => $request->competence,
                       'specialite_id' => $request->specialite,
                       'nombreexperience' => $request->dureexperience,
                       'uniteexperience_id' => $request->uniteduree,
                       'expertise' => $request->expertise,
                       'niveauetude_id' => $request->niveauetude,
                       'diplome_id' => $request->diplome,
                       'paysdiplome_id' => $request->paysdiplome,
                       'etablissementfrequente' => $request->etablissementfrequente,
                       'typeenseignement_id' => $request->typeenseignement,
                       'typeetablissement_id' => $request->typeetablissement,
                       'datenaissance' => $request->datenaissance,
                       'lieunaissance' => $request->lieunaissance,
                       'paysnationalite_id' => $request->nationalite,
                       'sexe_id' => $request->sexe,
                       'typepieceidentite_id' => $request->typeidentite,
                       'numerocni' => $request->numeropiece,
                       'situationmatrimoniale_id' => $request->situationmatrimonial,
                       'divisionregionaleaej_id' => $request->division,
                       'paysresidence_id' => $request->paysresidence,
                       'villeresidence_id' => $request->villeresidence,
                       'agencecnps_id' => $request->agencecnps,
                       'lieuhabitation' => $request->lieuhabitation,
                       'nomdupere' => $request->pere,
                       'nomdelamere' => $request->mere,
                       'datedebutchomage' => $request->debutchomage,
                       'motifinscription_id' => $request->motifinscription,
                       'typesituationhandicap_id' => $request->situationhandicap,
                       'precision_handicap' => $request->handicapdemandeur,
                       'categoriedemandeur_id' => $request->categorie,
                       'conseillerprojet_id' => $request->conseiller,
                       'nocnps' => $request->numerocnps

                       ]);
              Session::flash('success',"Détails du demandeur modifiés avec succès");
            } catch (\Exception $exception){
             DB::rollback();
                Session::flash('error',"Une erreur s'est produite ".$e->getMessage().", Réessayer svp");
                return Redirect::back();
        }
    }


    public function editParametreConnexionById($request)
    {
        try {
              $pwd= $request->password;
              $confirm_pwd= $request->confirm_password;

              if($pwd==$confirm_pwd){

                 $affected = DB::table('users')
                              ->where('id', $request->user_id)
                              ->update([
                                        'password' => md5($request->nom)
                                       ]);
                Session::flash('success',"Mot de passe modifié avec succès");

              }else{
                 Session::flash('error',"Erreur les mot de passe sont diférents");
                 return Redirect::back();
              }
            

        } catch (\Exception $exception){
                Session::flash('error',"Une erreur s'est produite ".$e->getMessage().", Réessayer svp");
                return Redirect::back();
        }


    }




}


