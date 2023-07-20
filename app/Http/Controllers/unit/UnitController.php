<?php

namespace App\Http\Controllers\unit;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\UnitPeople;
use App\Models\UnitPet;
use App\Models\UnitVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

            foreach ($peoples as $pkey => $pValue) {
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

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'birthdate' => ['required', 'date'],
        ]);

        if (!$validator->fails()) {
            $name = $request->input('name');
            $birthdate = $request->input('birthdate');

            $newPerson = new UnitPeople();
            $newPerson->id_unit = $id;
            $newPerson->name = $name;
            $newPerson->birthdate = $birthdate;
            $newPerson->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function addVehicle(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'color' => ['required'],
            'plate' => ['required'],
        ]);

        if (!$validator->fails()) {
            $title = $request->input('title');
            $color = $request->input('color');
            $plate = $request->input('plate');

            $newVehicle = new UnitVehicle();
            $newVehicle->id_unit = $id;
            $newVehicle->title = $title;
            $newVehicle->color = $color;
            $newVehicle->plate = $plate;
            $newVehicle->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function addPet(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'race' => ['required'],
        ]);

        if (!$validator->fails()) {
            $name = $request->input('name');
            $race = $request->input('race');

            $newPet = new UnitPet();
            $newPet->id_unit = $id;
            $newPet->name = $name;
            $newPet->race = $race;
            $newPet->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function removePerson($id)
    {
        $array = ['error' => ''];

        return $array;
    }
}
