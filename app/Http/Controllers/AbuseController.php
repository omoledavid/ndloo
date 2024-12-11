<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ReportAbuseRequest;
use App\Models\User;
use App\Support\Services\AbuseService;
use Illuminate\Http\JsonResponse;

class AbuseController extends Controller
{
    public function __construct(private readonly AbuseService $abuseService) {}

    public function report(ReportAbuseRequest $request, User $account): JsonResponse
    {
        return $this->abuseService->report($request, $account);
    }
}
