<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use App\Mail\MailOffreEmploiPostulation;
use App\Mail\SendMailDemandeurToConseillerEmploi;
use App\Models\BackendUser;
use App\Models\DemandeurEmploi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DemandeurToConseillerEmploi extends Controller
{

    public function sendMail(Request $request){

        try {
            $demandeur = Auth::user()->demandeur;
            $conseilleremploi = BackendUser::find($demandeur->conseilleremploi_id);
            Mail::to($conseilleremploi->email)->send(new SendMailDemandeurToConseillerEmploi($request->message));
            if (Mail::failures()) {
                $message =  ["status" => "failed", "message" => "Le message envoyé par mail a échoué."];
            } else {
                $message =  ["status" => "success", "message" => "Votre mail a bien été envoyé !!!"];
            }
        } catch (\Exception $exception){
            $message =  ["status" => "failed", "message" => $exception->getMessage()];
        }

        return response()->json($message);
    }
}
