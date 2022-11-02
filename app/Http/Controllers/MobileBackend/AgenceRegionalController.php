<?php

namespace App\Http\Controllers\MobileBackend;

use App\Http\Controllers\Controller;
use App\Models\AgenceRegionale;
use Illuminate\Http\Request;

class AgenceRegionalController extends Controller
{
    public function index() {
        $d = [];
        $agenceregionales = AgenceRegionale::all();
        foreach ($agenceregionales as $item) {
            $d[]=[
               'id'             => $item->id,
               'nom'            => $item->nom,
               'code'           => $item->code,
               'latitude'       => $item->latitude,
               'longitude'      => $item->longitude,
               'contact'        => $item->contact,
               'localisation'   => $item->localisation,
               'created_at'     => $item->created_at,
               'updated_at'     => $item->updated_at,
               'deleted_at'     => $item->deleted_at,
               'migration_key'  => $item->migration_key,
               'actif'          => $item->actif,
            ];
        }
        return response()->json($d);
    }
}
