<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code_gen;
    public $nom_prenom;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code_gen,$nom_prenom)
    {
        $this->code_gen = $code_gen;
        $this->nom_prenom = $nom_prenom;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@agenceemploijeunes.ci')
            ->subject('Réinitialisation du mot de passe')
            ->markdown('emails.auth.resetpasswordcodemail')
            ->with([ 'code_gen' =>  $this->code_gen,'nom_prenom' => $this->nom_prenom]);
    }
}
