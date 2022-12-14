<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailDemandeurToConseillerEmploi extends Mailable
{
    use Queueable, SerializesModels;

    protected $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@agenceemploijeunes.ci')
                    ->subject('Message de demandeur')
                    ->markdown('emails.conseilleremploi.sendmaildemandeurtoconseilleremploi')
                    ->with(['message' => $this->message]);
    }
}
