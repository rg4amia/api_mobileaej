<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use App\Jobs\Mobile\ResetPasswordByMailJob;
use App\Mail\ResetPasswordCodeMail;
use App\Models\DemandeurEmploi;
use App\Models\OffreEmploiDemandeur;
use App\Models\OffreFormationDemandeur;
use App\Models\ProjetFinancement;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class AuthenticateController extends Controller
{
    public function login(Request $request)
    {
        $rules = array(
            'email'	    => 'required|email',
            'password'	=> 'required|string|min:2',
        );

        $messages = [
            'email.required'    => 'L\' adresse email est obligatoire',
            'email.email'       => 'le format email n\'est pas correcte',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min'      => 'Accepte au moins 2 caractères.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            return response()->json([
                "errorsvalidation" => [
                    "password"  => $validator->errors()->first('password'),
                    'email'     => $validator->errors()->first('email')]
            ] ,402);

        }

        $user = User::where("email", $request->email)->first();

        if (is_null($user)) {

            return response()->json([
                "status" => "failed",
                "errors" => "Email ou Mot de Passe invalide"
            ],401);

        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user  = Auth::user();
            $token = $user->createToken('token')->plainTextToken;

            $demandeur = DemandeurEmploi::with('agenceregionale','niveauetude','typepieceidentite','diplome','specialite','statudemandeur','guichetemploi','conseilleremploi')->find($user->demandeuremploi_id);

            $nbreemploipostulation = OffreEmploiDemandeur::where('demandeur_id', $demandeur->id)->count();
            $nbreformationpostulation = OffreFormationDemandeur::where('demandeur_id', $demandeur->id)->count();
            $nbreprojet = ProjetFinancement::where('demandeur_id', $demandeur->id)->count();

            return response()->json([
                'success'                   => true,
                'token'                     => $token,
                'user'                      => Auth::user(),
                'nbreformation'             => $nbreformationpostulation,
                'nbreemploi'                => $nbreemploipostulation,
                'nbreprojet'                => $nbreprojet,
                'demandeur'                 => $demandeur,
                'profile'                   => $demandeur->photoProfile()
            ],200);

        } else {

            return response()->json([
                'success' => false,
                'errors'  => 'email ou mot de passe invalide',
            ], 401);
        }
    }

    public function resetPassword(Request $request){

        $rules = array(
            'email_telephone'	    => 'required|string',
        );

        $messages = [
            'email_telephone.required'    => 'L\' adresse email est obligatoire',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            return response()->json([
                    "status" => "failed",
                    "message" => $validator->errors()->first('email_telephone')
                ],200);
        }

        $demandeur = DemandeurEmploi::where('telephone',$request->email_telephone)->first();

        $code_generate = Str::random(4);

        if($demandeur){
            $demandeur->user->reset_password_code = $code_generate;
            $demandeur->user->save();

            ResetPasswordByMailJob::dispatch($code_generate, $demandeur->user->email ,$demandeur->nom .' '. $demandeur->prenoms );

        } else {
            $user = User::where('email',$request->email_telephone)->first();
            if ($user) {
                $user->reset_password_code = $code_generate;
                $user->save();
                ResetPasswordByMailJob::dispatch($code_generate, $user->email ,$user->demandeur->nom .' '. $user->demandeur->prenoms);
               // Mail::to($user->email)->send(new ResetPasswordCodeMail($code_generate,$user->demandeur->nom .' '. $user->demandeur->prenoms));
                if (Mail::failures()) {
                    Log::warning( 'Mail: Désolé ! Veuillez réessayer ce dernier' );
                }
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Email ou le numéro téléphone n'existe pas."
                ],200);
            }
        }

        return response()->json([
            "status" => "success",
            "message" => "Le code de reinitialisation du mot de passe envoyé par e-mail!"
        ],200);
    }

    public function verifResetPassword(Request $request){

        $rules = array(
            'code'	    => 'required|string',
        );

        $messages = [
            'code.required'  => 'Le code de reinitialisation est obligatoire',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                "status" => "failed",
                "message" => $validator->errors()->first('code')
            ],200);
        }

        $user = User::where('reset_password_code',$request->code)->first();

        if($user){
           return response()->json(
               [
                   "status"     => "success",
                   "message"    => "Code verifier avec success",
                   "user"       => $user
               ]
               );
        } else {
            return response()->json([
                "status" => "failed",
                "message" => "Code invalide où a expiré"
            ]);
        }
    }




    public function changeResetPassword(Request $request){

       /* $rules = array(
            'code'	            => 'required|string',
            'password'          => 'required',
            'password_confirm'  => 'sometimes|same:password',
        );

        $messages = [
            'code.required'  => 'Le code de reinitialisation est obligatoire',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                "status" => "failed",
                "message" => $validator->errors()->first('code')
            ],200);
        }*/

        $user = User::where('reset_password_code',$request->code)->first();

        if($user){
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(
                [
                    "status"     => "success",
                    "message"    => "Votre mot de passe modifié avec succès",
                 ]
            );
        } else {
            return response()->json([
                "status" => "failed",
                "message" => "Une erreur c'est produite pendant la modification"
            ]);
        }
    }

    public function addUserIdOneSignal(Request $request){

        try {
           // $onesignalId = array();
            $demandeur = Auth::user()->demandeur;

            $demandeurModel = DemandeurEmploi::find($demandeur->id);
            $onesignalId = json_decode($demandeurModel->onesignale_id);

            if ($onesignalId) {
                array_push($onesignalId, $request->onesignale_id);
                $data['onesignale_id'] =  $onesignalId;
            } else {
                $data['onesignale_id'] = [(string)$request->onesignale_id];
            }

            $demandeurModel->update($data);

            Log::info($demandeurModel);

            return response()->json(
                [
                    "status"     => "success",
                    "message"    => "Votre mot de passe modifié avec succès",
                ]
            );

        } catch (\Exception $e) {

            return response()->json(
                [
                    "status"     => "failed",
                    "message"    => $e->getMessage(),
                ]
            );

        }
    }
}
