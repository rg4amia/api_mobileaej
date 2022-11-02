<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OneSignal;

class OneSignalManageController extends Controller
{
    public function sendNotifcationPush(){
        $client = new \GuzzleHttp\Client();
        //$data = \request('id');
        //dd($data);    "include_player_ids":["'. $data.'"],

        $response = $client->request('POST', 'https://onesignal.com/api/v1/notifications', [
            'body' => '{
            "app_id" : "84bfcfa5-f200-461d-99af-f46c1de78bf7",
            "included_segments":["Subscribed Users"],
            "contents":{"en":"'.\request('contenu').'"},
            "name":"INTERNAL_CAMPAIGN_NAME",
            "headings": {"en":"'.\request('titre').'"}
            }',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic OTNmZTczZGMtYjYzNi00NDM3LTg3NmMtZTY0NDM1Y2JkMjA5',
                'Content-Type' => 'application/json',
            ],
        ]);

        if($response->getStatusCode() == 200){
            $data = [
                'success' => true,
                'message' => 'push envoye avec succes'
            ];
        }

        return response()->json($data);
        //echo ;
    }
}
