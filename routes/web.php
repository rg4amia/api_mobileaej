<?php

use App\Http\Controllers\Api\ApiController;

use App\Http\Livewire\Ministere\MinistereRegister;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
    Route::get('/demandeur-info/{matricule}/{token?}', [ApiController::class, 'demandeur_info'])->name('demandeur_info');
    Route::get('/offre-emplois-bcpe', [ApiController::class, 'offre_emplois_bcpe'])->name('offre_emplois_bcpe');
    Route::get('/list-offre-emplois', [ApiController::class, 'list_offre_emplois'])->name('offre_emplois');
    Route::get('/list-offre-formation', [ApiController::class, 'list_offre_formation'])->name('offre_formation');
    Route::get('/emploi-jeune-tv', [ApiController::class, 'emploi_jeune_tv'])->name('emploi_jeune_tv');
    Route::get('/campagne-cour', [ApiController::class, 'campagne_cour'])->name('campagne_cour');
    Route::get('/list-slideshow', [ApiController::class, 'list_slideshow'])->name('list-slideshow');
    Route::get('/actualites-slideshow', [ApiController::class, 'actualites_slideshow'])->name('actualites-slideshow');
    Route::post('/autocomplete-commune', [ApiController::class, 'autocompleteCommune'])->name('commune');
});
