<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use App\Models\DemandeurEmploi;
use Illuminate\Http\Request;
use OneSignal;

class OneSignalManageController extends Controller
{

    public function sendByUser(){

        $noreference = 'F-ARC-20220828-0632';
        $contenu    = 'Votre demande a été prise en compte ! Merci.';
        $titre      = 'Postulation a l\'offre ' . $noreference;

        $demandeur = DemandeurEmploi::find(124099);

        $params = [];
        $params['include_player_ids'] = json_decode($demandeur->onesignale_id);

        $contents = [
            "en" => $contenu,
        ];

        $headings = [
            "en" => $titre,
        ];
        $params['contents'] = $contents;
        $params['headings'] = $headings;
        //$params['delayed_option'] = "timezone"; // Will deliver on user's timezone
        //$params['delivery_time_of_day'] = "17:05PM"; // Delivery time
        OneSignal::sendNotificationCustom($params);

        //sendByUser($demandeur->onesignale_id, $contenu, $titre);

       /*  $noreference = 'F-ARC-20220828-0632';
        $contenu    = 'Votre demande a été prise en compte ! Merci.';
        $titre      = 'Postulation a l\'offre ' . $noreference;

        $userId = "7df4a582-0ae7-418f-8b0b-ddc52f180ede";
        $params = [];
        $params['include_player_ids'] = [$userId];
        $contents = [
            "en" => $contenu,
        ];
        $headings = [
            "en" => $titre,
        ];
        $params['contents'] = $contents;
        $params['headings'] = $headings; */

        //$params['delayed_option'] = "timezone"; // Will deliver on user's timezone
        //$params['delivery_time_of_day'] = "17:05PM"; // Delivery time

       /*  OneSignal::sendNotificationCustom($params);

        return [true]; */
    }
}
