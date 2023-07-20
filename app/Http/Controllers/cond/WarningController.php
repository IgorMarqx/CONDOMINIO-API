<?php

namespace App\Http\Controllers\cond;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Warning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    public function addWarningFile(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'file', 'mimes:jpg,png'],
        ]);

        if (!$validator->fails()) {
            $file = $request->file('photo')->store('public');

            $array['photo'] = asset(Storage::url($file));
        } else {
            $array['error'] =   $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function setWarning(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(),  [
            'title' => ['required'],
            'property' => ['required'],
        ]);

        if (!$validator->fails()) {
            $title = $request->input('title');
            $property = $request->input('property');
            $list = $request->input('list');

            $newWarning = new Warning();
            $newWarning->id_unit = $property;
            $newWarning->title = $title;
            $newWarning->status = 'IN_REVIEW';
            $newWarning->created_at = date('Y-m-d');

            if ($list && is_array($list)) {
                $photos = [];

                foreach ($list as $listItem) {
                    $url = explode('/', $listItem);
                    $photos[] = end($url);
                }

                $newWarning->photos = implode(',', $photos);
            }else{
                $newWarning->photos = ' ';
            }

            $newWarning->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
}
