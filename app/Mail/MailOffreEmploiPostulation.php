<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailOffreEmploiPostulation extends Mailable
{
    use Queueable, SerializesModels;
    public $nom_demandeur;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nom_demandeur)
    {
        $this->nom_demandeur = $nom_demandeur;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@agenceemploijeunes.ci')
            ->subject('Confirmation candidature')
            ->markdown('emails.offreemploi.postule')
            ->with([ 'nom' =>  $this->nom_demandeur]);
    }
}
