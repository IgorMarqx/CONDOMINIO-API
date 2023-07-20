<?php

namespace App\Http\Controllers\unit;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\UnitPeople;
use App\Models\UnitPet;
use App\Models\UnitVehicle;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function getInfo($id)
    {
        $array = ['error' => ''];

        $unit = Unit::find($id);
        if ($unit) {
            $peoples = UnitPeople::where('id_unit', $id)->get();
            $vehicles = UnitVehicle::where('id_unit', $id)->get();
            $pets = UnitPet::where('id_unit', $id)->get();

            foreach($peoples as $pkey => $pValue){
                $peoples[$pkey]['birthdate'] = date('d/m/Y', strtotime(($pValue['birthdate'])));
            }

            $array['peoples'] = $peoples;
            $array['vehicles'] = $vehicles;
            $array['pets'] = $pets;
        } else {
            $array['error'] = 'Propriedade não encontrado.';
            return $array;
        }

        return $array;
    }

    public function addPerson(Request $request, $id)
    {
        $array = ['error' => ''];

        return $array;
    }

    public function addVehicle(Request $request, $id)
    {
        $array = ['error' => ''];

        return $array;
    }

    public function addPet(Request $request, $id)
    {
        $array = ['error' => ''];

        return $array;
    }
}
