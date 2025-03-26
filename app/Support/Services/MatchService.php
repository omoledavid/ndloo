<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Contracts\DataObjects\LocationData;
use App\Models\ProfileInfo;
use App\Support\Helpers\MatchQueryBuilder;
use App\Support\Helpers\NearestLocation;
use Illuminate\Http\JsonResponse;

class MatchService extends BaseService
{
    public function matches(object $request): JsonResponse
    {
        $user = $request->user();
        $matchArr = [];

        $profiles = (new MatchQueryBuilder($request))
//            ->countryFilter()
            ->nameFilter()
            ->ageFilter()
//            ->genderFilter()
            ->activeFilter()
            ->get();

        $data = $request->query('latitude') && $request->query('longitude')
            ? LocationData::fromRequest($request)
            : $user;

        foreach ($profiles as $profile) {
            $distance = NearestLocation::differenceInKm($data, $profile);

            $matchArr[$distance] = [
                'profile' => $profile,
                'distance' => $distance,
            ];
        }

        $matches = $request->query('nearby') ? collect($matchArr)->sortKeys()->values() : collect($matchArr)->values();

        return $this->successResponse(data: [
            'infos' => ProfileInfo::all()->groupBy('category'),
            'matches' => $matches,
        ]);
    }
}
