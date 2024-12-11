<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BoostPlan;
use App\Support\Services\BoostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoostController extends Controller
{
    public function __construct(private readonly BoostService $boostService) {}

    public function plans(): JsonResponse
    {
        return $this->boostService->plans();
    }

    public function boost(Request $request, BoostPlan $plan): JsonResponse
    {
        return $this->boostService->boost($request, $plan);
    }
}
