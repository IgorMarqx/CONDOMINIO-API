<?php

namespace App\Http\Controllers\cond;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function getReservations()
    {
        $array = ['error' => ''];

        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];

        $areas = Area::where('allowed', 1)->get();

        foreach ($areas as $area) {
            $dayList = explode(',', $area['days']);

            $dayGroups = [];

            // Adicionando primeiro item
            $lastDay = intval(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);

            // Adicionando dias relevantes
            foreach ($dayList as $day) {
                if (intval($day) != $lastDay + 1) {
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];
                }

                $lastDay = intval($day);
            }

            // Adicionando o ultimo dia
            $dayGroups[] = $daysHelper[end($dayList)];

            // Juntanto as datas
            $dates = '';
            $close = 0;
            foreach ($dayGroups as $group) {
                if ($close == 0) {
                    $dates .= $group;
                } else {
                    $dates .= '-' . $group . ',';
                }
                $close = 1 - $close;
            }

            $dates = explode(',', $dates);
            array_pop($dates);

            // Adicionando o time
            $start = date('H:i', strtotime($area['start_time']));
            $end = date('H:i', strtotime($area['end_time']));

            foreach ($dates as $dKey => $dValue) {
                $dates[$dKey] .= ' ' . $start . 'às' . $end;
            }

            $array['list'] = [
                'id' => $area['id'],
                'cover' => asset('storage/' . $area['cover']),
                'title' => $area['title'],
                'dates' => $dates,
            ];
        }

        return $array;
    }

    public function setReservation(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'id_area' => ['required'],
            'reservation_date' => ['required', 'date'],
        ]);

        if (!$validator->fails()) {
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
}
