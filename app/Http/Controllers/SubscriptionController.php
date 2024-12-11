<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Support\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function plans(): JsonResponse
    {
        return $this->subscriptionService->plans();
    }

    public function subscribe(Request $request, SubscriptionPlan $plan): JsonResponse
    {
        return $this->subscriptionService->subscribe($request, $plan);
    }

    public function unsubscribe(Request $request, SubscriptionPlan $plan): JsonResponse
    {
        return $this->subscriptionService->unsubscribe($request, $plan);
    }
}
