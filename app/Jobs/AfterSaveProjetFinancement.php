<?php

namespace App\Jobs;

use App\Mail\MailAfterSaveProjetFinancement;
use App\Mail\MailNewPromoteurProjetFinancement;
use App\Models\DemandeurEmploi;
use App\Models\ProjetFinancement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AfterSaveProjetFinancement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $projet;
    public $demandeur;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ProjetFinancement $projet)
    {
        $this->projet = $projet;
        $this->demandeur = DemandeurEmploi::findOrFail($projet->demandeur_id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->projet->matriculeprojet = $this->generateCode($this->projet->id);

        $chefsAgenceRegionale   = DB::table('backend_users')
                                    ->where('role_id',7)
                                    ->where('divisionregionaleaej_id',$this->projet->divisionregionaleaej_id)
                                    ->get();

        //envoi de mail au chef d'ingenierie projet
        foreach ($chefsAgenceRegionale as $destinataire) {
            $vars = ['promoteur' => $this->demandeur->matriculeaej.' - '.$this->demandeur->nom.' '.$this->demandeur->prenoms,
                'projet' => $this->projet->matriculeprojet.' - '.$this->projet->titreprojet
            ];
            //recuperation de l email du destinataire //activer apres
           // Mail::to($destinataire->email)->send(new MailAfterSaveProjetFinancement($this->demandeur,$this->projet));
        }

        //mail de confirmation de création de projet
        //recuperation de l email du destinataire
        $user = User::where('demandeuremploi_id',$this->demandeur->id)->first();
        Mail::to($user->email)->send(new MailNewPromoteurProjetFinancement($this->projet->matriculeprojet));

        $this->projet->save();
    }

    protected function generateCode($order){
        $dayorder = ProjetFinancement::where('created_at','>=',date('Y-m-d'))->count(); ;//l'ordre du jour
        return $order.'-'.$dayorder;
    }
}
