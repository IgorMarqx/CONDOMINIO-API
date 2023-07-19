<?php

namespace App\Http\Controllers\cond;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Warning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarningController extends Controller
{
    public function getMyWarnings(Request $request)
    {
        $array = ['error' => ''];

        $property = $request->input('property');

        $user = Auth::user();

        if ($property) {
            $unit = Unit::where('id', $property)
                ->where('id_owner', $user['id'])
                ->count();

            if ($unit > 0) {

                $warnings = Warning::where('id_unit', $property)
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->get();

                foreach ($warnings as $warningKey => $warningValue) {
                    $warnings[$warningKey]['created_at'] = date('d/m/Y', strtotime($warningValue['created_at']));
                    $photoList = [];
                    $photos = explode(',', $warningValue['photos']);

                    foreach ($photos as $photo) {
                        if (!empty($photo)) {
                            $photoList[] = asset('storage/' . $photo);
                        }
                    }

                    $warnings[$warningKey]['photos'] = $photoList;
                }

                $array['list'] = $warnings;
            } else {
                $array['error'] = 'Esta unidade não é sua.';
            }
        } else {
            $array['error'] = 'Insira uma propriedade.';
        }


        return $array;
    }
}
