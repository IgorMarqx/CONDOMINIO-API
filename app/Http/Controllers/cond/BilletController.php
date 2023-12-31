<?php

namespace App\Http\Controllers\cond;

use App\Http\Controllers\Controller;
use App\Models\Billet;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BilletController extends Controller
{
    public function getAll(Request $request)
    {
        $array = ['error' => ''];

        $property = $request->input('property');

        $user = Auth::user();

        if ($property) {
            $unit = Unit::where('id', $property)
                ->where('id_owner', $user['id'])
                ->count();

            if ($unit > 0) {
                $billets = Billet::where('id_unit', $property)->get();

                foreach ($billets as $billetKey => $billetValue) {
                    $billets[$billetKey]['fileurl'] = asset('storage/' . $billetValue['fileurl']);
                }

                $array['list'] = $billets;
            } else {
                $array['error'] = 'Esta unidade não é sua.';
            }
        } else {
            $array['error'] = 'A propriedade é obrigatória.';
        }

        return $array;
    }
}
