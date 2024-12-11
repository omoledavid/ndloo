<?php

namespace App\Http\Controllers;

use App\Models\GiftPlan;
use App\Models\User;
use App\Models\UserGift;
use App\Support\Services\GiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    public function __construct(private readonly GiftService $giftService) {}

    public function plans(): JsonResponse
    {
        return $this->giftService->plans();
    }

    public function purchase(GiftPlan $plan, User $recipient): JsonResponse
    {
        return $this->giftService->purchase($plan, $recipient);
    }

    public function myPlans(): JsonResponse
    {
        return $this->giftService->myPlans();
    }

    public function redeemGift(UserGift $gift, Request $request): JsonResponse
    {
        return $this->giftService->redeem($gift, $request);
    }
}
