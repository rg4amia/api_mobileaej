<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffreFormationDemandeur extends Model
{
    use HasFactory;

    public $table = 'digit_demandeur_formation_demandeur';

    public function formation(){
        return $this->belongsTo(Formation::class,'formation_id','id');
    }

    public function getPostulantStatutAttribute() {

        if ($this->formation->statutformation_id == null)
            return "Traitement en cours";

        if ($this->formation->statutformation_id == 1)
            return "Traitement en cours";

        if ($this->formation->statutformation_id == 2)
            return "Formation fermée";

        if ($this->formation->statutformation_id == 3) {
            if ($this->statut_demandeur_formation->id == 2)
                return "Refusé(e)";

            if ($this->statut_demandeur_formation->id == 5)
                return "Retiré(e)";

            if ($this->statut_demandeur_formation->id == 1) {
                if ($this->motifabandonformation_id) {
                    return "Abandon";
                } else {
                    return "Formation aboutie";
                }
            }
            if ($this->statut_demandeur_formation->id == 4 or
                $this->statut_demandeur_formation->id == 3)
                return "Non retenu(e)";
        }

        if ($this->formation->statutformation_id == 4)
            return "Formation annulée";


        if ($this->formation->statutformation_id == 5) {
            return $this->statut_demandeur_formation->short_code;
        }
    }

    public function getProgressionAttribute() {
        $progress = '';
        if (in_array($this->postulantStatut, ["Formation aboutie"])) {
            $progress = "success";
        }

        if (in_array($this->postulantStatut, ["Traitement en cours", "Présélectionné(e)", "Retenu(e)", "En cours", "En réserve"])) {
            $progress = "info";
        }

        if (in_array($this->postulantStatut, ["Formation fermée", "Formation annulée"])) {
            $progress = "warning";
        }

        if (in_array($this->postulantStatut, ["Refusé(e)", "Retiré(e)", "Abandon", "Non retenu(e)"])) {
            $progress = "danger";
        }

        return $progress;
    }

    //recuperation de toutes les formations fcq aux quelles le demandeur a postuler

    public function loadFormationsFcq() {

        $query = null;

        if (auth()->user()) {
            $demandeur = auth()->user()->demandeur;
           // dd($demandeur);
            $query = static::whereHas('formation', function ($query) {
                $query->where('categorieformation_id', 1);
            })->where('demandeur_id', $demandeur->id)->paginate(100);
        }

        return $query;
    }

    //recuperation de toutes les formations passepor aux quelles le demandeur a postuler

    public function loadFormationsMppe(){

        $query = null;

        if (auth()->user()) {
            $demandeur = auth()->user()->demandeur;
            $query = static::whereHas('formation', function ($query) {
                $query->where('categorieformation_id', 2);
            })->where('demandeur_id', $demandeur->id)->paginate(100);
        }
        return $query;
    }

    //        return static::with('demandeur', 'offreemploi')->where('demandeur_id',$demandeur->id)->paginate(10);

    public function loadFormationAutre() {
        $query = null;
        if (auth()->user()) {
            $demandeur = auth()->user()->demandeur;
            $query = static::whereHas('formation', function ($query) {
                $query->whereNotIn('categorieformation_id', [$this->categorie_formation_fcq, $this->categorie_formation_mon_passseport]);
            })->where('demandeur_id', $demandeur->id)->paginate(10);
        }
        return $query;
    }

}
