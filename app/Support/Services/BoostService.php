<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Contracts\Enums\UserStates;
use App\Http\Resources\BoostPlanResource;
use App\Models\BoostPlan;
use App\Notifications\Subscriptions\ProfileBoostNotice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BoostService extends BaseService
{
    public function plans(): JsonResponse
    {
        return $this->successResponse(data: [
            'plans' => BoostPlanResource::collection(BoostPlan::all()),
        ]);
    }

    public function boost(object $request, BoostPlan $plan): JsonResponse
    {
        if ($plan->price > $request->user()->wallet) {
            return $this->errorResponse(__('responses.insufficientFunds'));
        }

        try {
            DB::beginTransaction();

            $expiryDate = Carbon::now()->addDays($plan->period)->toDateTimeString();
            $request->user()->update([
                'wallet' => $request->user()->wallet - $plan->price,
                'boosted' => UserStates::ACTIVE,
            ]);
            $plan->user()->attach($request->user(), ['expires_on' => $expiryDate]);

            DB::commit();

            $request->user()->notify(new ProfileBoostNotice($request->user(), $plan));

            return $this->successResponse(__('responses.planSubscribed', ['name' => $plan->period.'day']));
        } catch (\Throwable $th) {
            Log::error($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }
}
