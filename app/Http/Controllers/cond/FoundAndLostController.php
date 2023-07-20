<?php

namespace App\Http\Controllers\cond;

use App\Http\Controllers\Controller;
use App\Models\FoundAndLost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FoundAndLostController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $lost = FoundAndLost::where('status', 'LOST')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        $recovered = FoundAndLost::where('status', 'RECOVERED')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($lost as $lostKey => $lostValue) {
            $lost[$lostKey]['created_at'] = date('d/m/Y', strtotime($lostValue['created_at']));
            $lost[$lostKey]['photo'] = asset('storage/' . $lostValue['photo']);
        }

        foreach ($recovered as $recKey => $recValue) {
            $rec[$recKey]['created_at'] = date('d/m/Y', strtotime($recValue['created_at']));
            $rec[$recKey]['photo'] = asset('storage/' . $recValue['photo']);
        }

        $array['lost'] = $lost;
        $array['recovered'] = $recovered;

        return $array;
    }

    public function insert(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'description' => ['required'],
            'where' => ['required'],
            'photo' => ['required', 'file', 'mimes:png,jpg'],
        ]);

        if (!$validator->fails()) {
            $description = $request->input('description');
            $where = $request->input('where');
            $file = $request->file('photo')->store('public');
            $file = explode('public/', $file);
            $photo = $file[1];

            $newLost = new FoundAndLost();
            $newLost->status = 'LOST';
            $newLost->photo = $photo;
            $newLost->description = $description;
            $newLost->where = $where;
            $newLost->created_at = date('Y-m-d');
            $newLost->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function update(Request $request, $id)
    {
        $array = ['error' => ''];

        $status = $request->input('status');
        if ($status && in_array($status, ['LOST', 'RECOVERED'])) {

            $item = FoundAndLost::find($id);
            if ($item) {
                $item->status = $status;
                $item->save();
            } else {
                $array['error'] = 'Produto inexistente.';
                return $array;
            }
        } else {
            $array['error'] = 'Status inválido.';
            return $array;
        }

        return $array;
    }
}
