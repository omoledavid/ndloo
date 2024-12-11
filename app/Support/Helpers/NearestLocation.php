<?php

declare(strict_types=1);

namespace App\Support\Helpers;

use App\Contracts\DataObjects\LocationData;
use App\Models\User;

class NearestLocation
{
    public static function differenceInKm(User|LocationData $data, User $match): int
    {
        $lat1 = floatval($data->latitude);
        $lat2 = floatval($match->latitude);
        $lon1 = floatval($data->longitude);
        $lon2 = floatval($match->longitude);

        if (($lat1 === $lat2) && ($lon1 === $lon2)) {
            return 0;
        }

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);

        return intval($dist * 60 * 1.1515 * 1.609344); //get distance in Km
    }
}
