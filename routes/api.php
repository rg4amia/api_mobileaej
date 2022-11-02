<?php

use App\Http\Controllers\MobileBackend\DemandeurToConseillerEmploi;
use App\Http\Controllers\MobileBackend\MesFavorisController;
use App\Http\Controllers\MobileBackend\OneSignalManageController;
use App\Http\Controllers\MobileBackend\PentestController;
use App\Http\Controllers\MobileBackend\ProjetFinancementController;
use App\Http\Controllers\MobileBackend\SuivieDemandeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\MobileBackend\AuthenticateController;
use App\Http\Controllers\MobileBackend\OffreEmploiController;
use \App\Http\Controllers\MobileBackend\OffreFormationController;
use \App\Http\Controllers\MobileBackend\AgenceRegionalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/test', [PentestController::class, 'test']);
Route::post('/auth/login', [AuthenticateController::class, 'login']);
Route::post('/auth/reset-password',[AuthenticateController::class, 'resetPassword']);
Route::post('auth/verif-reset-password',[AuthenticateController::class, 'verifResetPassword']);
Route::post('auth/change-reset-password',[AuthenticateController::class, 'changeResetPassword']);
Route::get('send/notif',[OneSignalManageController::class,'sendNotifcationPush']);
Route::get('v1.0/list-agence-regionale',[AgenceRegionalController::class,'index']);
Route::get('v1.0/load-projet-parameter',[ProjetFinancementController::class,'loadDataParametre']);
Route::get('v1.0/filter-offre-emplois', [OffreEmploiController::class,'specialiteOffreEmploi']);
Route::get('v1.0/list-offre-emplois-withoutconnexion', [OffreEmploiController::class,'listOffreEmploisWithOutConnexion']);

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::middleware('auth:sanctum')->group(function() {

    Route::group(['prefix' => 'v1.0/suivie/'],function (){
        Route::get('offre-emploi-postule', [SuivieDemandeController::class,'suiviePostulation']);
        Route::get('formation-fcq-postule', [SuivieDemandeController::class,'suivieFormationFCQ']);
        Route::get('formation-mppe-postule', [SuivieDemandeController::class,'suivieFormationMPPE']);
        Route::get('projet-financement', [SuivieDemandeController::class,'suivieProjetFinancement']);
    });

    //conseiller emploi
    Route::post('v1.0/sendmail-demandeur-to-mail',[DemandeurToConseillerEmploi::class,'sendMail']);

    // save favoris
    Route::post('v1.0/creer-favoris',[MesFavorisController::class,'store']);
    Route::get('v1.0/index-favoris',[MesFavorisController::class,'index']);

    // projet de financement mobile
    Route::post('v1.0/creer-projet-financement',[ProjetFinancementController::class,'creerProjet']);

    //postulation a l'offre de formation mon passport
    Route::post('v1.0/postule-formation-mppe', [OffreFormationController::class,'onFormationPostulePasseport']);
    Route::post('v1.0/postule-formation-fcq', [OffreFormationController::class,'onFormationPostuleFCQ']);

    //Route::get('v1.0/list-offre-emplois/{specialite_id?}/{diplome_id?}/{typecontrat_id?}', [OffreEmploiController::class,'listOffreEmplois']);
    Route::get('v1.0/list-offre-emplois', [OffreEmploiController::class,'listOffreEmplois']);
    Route::get('v1.0/postule-offre-emplois/{offre_emploi_id}', [OffreEmploiController::class,'onEmploiPostule']);
    Route::get('v1.0/list-offre-emplois-bcpe', [OffreEmploiController::class,'offreEmploisBcpe']);
    Route::get('v1.0/list-offre-formation', [OffreFormationController::class,'listOffreFormation']);
    Route::get('v1.0/list-offre-formation/{id}', [OffreFormationController::class,'formationById']);
    Route::post('v1.0/postuler-offre-formation/{formation_id}', [OffreFormationController::class,'onPostuler']);
});
