<?php

namespace App\Mail;

use App\Models\DemandeurEmploi;
use App\Models\ProjetFinancement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailAfterSaveProjetFinancement extends Mailable
{
    use Queueable, SerializesModels;

    public $projet;
    public $demandeur;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DemandeurEmploi $demandeur,ProjetFinancement $projet)
    {
        $this->projet       = $projet;
        $this->demandeur    = $demandeur;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('r4gamia@gmail.com')
                    ->subject('Projet AEJ')
                    ->markdown('emails.aftersaveprojetfinancement')->with([
                        'promoteur' => $this->demandeur->matriculeaej.' - '.$this->demandeur->nom.' '.$this->demandeur->prenoms,
                        'projet' => $this->projet->matriculeprojet.' - '.$this->projet->intituleprojet ]);
    }
}
