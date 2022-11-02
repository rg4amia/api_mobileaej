<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailNewPromoteurProjetFinancement extends Mailable
{
    use Queueable, SerializesModels;

    public $matriculeprojet;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($matriculeprojet)
    {
        $this->matriculeprojet = $matriculeprojet;
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
            ->markdown('emails.newpromoteurprojetfinancement')
            ->with(['projet' => $this->matriculeprojet]);
    }
}
