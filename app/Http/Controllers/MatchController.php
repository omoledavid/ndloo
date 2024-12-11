<?php

namespace App\Http\Controllers;

use App\Support\Services\MatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function __construct(private readonly MatchService $matchService) {}

    public function matches(Request $request): JsonResponse
    {
        return $this->matchService->matches($request);
    }
}
