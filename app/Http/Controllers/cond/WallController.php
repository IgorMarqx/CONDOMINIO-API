<?php

namespace App\Http\Controllers\cond;

use App\Http\Controllers\Controller;
use App\Models\Wall;
use App\Models\WallLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WallController extends Controller
{
    public function getAll()
    {
        $array = ['error' => '', 'list' => []];

        $user = Auth::user();

        $walls = Wall::all();

        foreach ($walls as $wallKey => $wallValue) {
            $walls[$wallKey]['likes'] = 0;
            $walls[$wallKey]['liked'] = false;

            $likes = WallLike::where('id_wall', $wallValue['id'])->count();
            $walls[$wallKey]['likes'] = $likes;

            $meLikes = WallLike::where('id_wall', $wallValue['id'])
                ->where('id_user', $user['id'])
                ->count();

            if ($meLikes > 0) {
                $walls[$wallKey]['liked'] = true;
            }
        }

        $array['list'] = $walls;


        return $array;
    }

    public function like($id)
    {
        $array = ['error' => ''];

        $user = Auth::user();

        $meLikes = WallLike::where('id_wall', $id)
            ->where('id_user', $user['id'])
            ->count();

        if ($meLikes > 0) {
            WallLike::where('id_wall', $id)
                ->where('id_user', $user['id'])
                ->delete();

            $array['liked'] = false;
        } else {
            $newLike = WallLike::create([
                'id_wall' => $id,
                'id_user' => $user['id'],
            ]);
            $newLike->save();

            $array['liked'] = true;
        }

        $array['likes'] = WallLike::where('id_wall', $id)->count();

        return $array;
    }
}
