<?php
/**
 * Created by PhpStorm.
 * Users: Achija
 * Date: 10/13/17
 * Time: 6:28 AM
 */


use App\Models\Formation;
use Illuminate\Support\Facades\DB;


if( ! function_exists('sendNotificationAllUser') )
{
    function sendNotificationAllUser($contenu,$titre,array $data = null,$sous_titre = null,$url=null){

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://onesignal.com/api/v1/notifications', [
            'body' => '{
            "app_id" : "84bfcfa5-f200-461d-99af-f46c1de78bf7",
            "included_segments":["Subscribed Users"],
            "contents":{"en":"'.$contenu.'"},
            "name":"INTERNAL_CAMPAIGN_NAME",
            "headings": {"en":"'.$titre.'"},
            "subtitle": {"en":"'.$sous_titre.'"},
            "data" :'.$data.',
            "url":'.$url.'
            }',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic OTNmZTczZGMtYjYzNi00NDM3LTg3NmMtZTY0NDM1Y2JkMjA5',
                'Content-Type' => 'application/json',
            ],
        ]);

        $data = [
            'success' => true,
            'message' => 'push envoye avec succes',
            'status' => $response->getStatusCode()
        ];
        return $data;
    }
}
// 'include_player_ids' => array("6392d91a-b206-4b7b-a620-cd68e32c3a76","76ece62b-bcfe-468c-8a78-839aeaa8c5fa","8e0f21fa-9a5a-4ae7-a9a6-ca1f24294b86")

if( ! function_exists('sendNotificationByUser') )
{
    function sendNotificationByUser($contenu,$titre,array $player_ids,array $data = null,$sous_titre = null,$url=null){

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://onesignal.com/api/v1/notifications', [
            'body' => '{
            "app_id" : "84bfcfa5-f200-461d-99af-f46c1de78bf7",
            "included_segments":["Subscribed Users"],
            "contents":{"en":"'.$contenu.'"},
            "name":"INTERNAL_CAMPAIGN_NAME",
            "headings": {"en":"'.$titre.'"},
            "subtitle": {"en":"'.$sous_titre.'"},
            "data" :'.$data.',
            "url":'.$url.',
            "include_player_ids":'. $player_ids.'
            }',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic OTNmZTczZGMtYjYzNi00NDM3LTg3NmMtZTY0NDM1Y2JkMjA5',
                'Content-Type' => 'application/json',
            ],
        ]);

        $data = [
            'success' => true,
            'message' => 'push envoye avec succes',
            'status' => $response->getStatusCode()
        ];

        return $data;
    }
}

if( ! function_exists('getagencesession') )
{
    function getagencesession()
    {
        $session = session()->get('orig_agence');
        return $session;
    }
}

if( !function_exists('demandeurPeutPostulerFormation')){
    function demandeurPeutPostulerFormation($formationsDemandeur){
        try{
            $estEllegible = true;
            foreach($formationsDemandeur as $formationDemandeur){
                $formation = Formation::find($formationDemandeur->formation_id);
                if (in_array($formation->statutformation_id, array(5))) {
                    if (in_array($formationDemandeur->statut_demandeur_formation_id, array(1))) {
                        $estEllegible = false;
                    }
                }
            }
            return $estEllegible;
        }catch(Exception $e){
            \Log("Une erreur est survenue lors de la vérification de l'éllégibilité de postulatiion d'une offre de formation, Raison :" . $e->getMessage());
        }
    }
}

if(!function_exists('getInstanceName'))
{
   function getInstanceName($table,$table_id,$value,$returnName)
   {

     $entree =DB::table($table)
             ->where([
                       $table_id =>$value
                  ])
             ->first();

    if($entree){
        $instance= $entree->$returnName;
                }else{
                    $instance= '';
                    }

     return $instance;

   }
}


