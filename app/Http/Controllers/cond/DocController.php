<?php

namespace App\Http\Controllers\cond;

use App\Http\Controllers\Controller;
use App\Models\Doc;
use Illuminate\Http\Request;

class DocController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $docs = Doc::all();

        foreach ($docs as $docKey => $docValue) {
            $docs[$docKey]['fileurl'] = asset('storage/' . $docValue['fileurl']);
        }

        $array['list'] = $docs;

        return $array;
    }
}
