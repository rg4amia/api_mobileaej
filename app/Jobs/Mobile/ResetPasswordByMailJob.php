<?php

namespace App\Jobs\Mobile;

use App\Mail\ResetPasswordCodeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResetPasswordByMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $code_generation;
    public $nomprenom;
    public $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($code_generation,$email,$nomprenom)
    {
        $this->nomprenom        = $nomprenom;
        $this->email            = $email;
        $this->code_generation  = $code_generation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new ResetPasswordCodeMail($this->code_generation, $this->nomprenom));
        if (Mail::failures()) {
            Log::warning( 'Mail: Désolé ! Veuillez réessayer ce dernier' );
        }
    }
}
