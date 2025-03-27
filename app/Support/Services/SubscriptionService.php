<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Contracts\Enums\SubscriptionStatus;
use App\Contracts\Enums\TransactionDuration;
use App\Models\SubscriptionCategory;
use App\Models\SubscriptionPlan;
use App\Notifications\Subscriptions\SubscriptionNotice;
use App\Notifications\Subscriptions\UnsubscriptionNotice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService extends BaseService
{
    public function plans(): JsonResponse
    {

        $subscriptionCategories = SubscriptionCategory::with(['plans' => function ($query) {
            $query->where('status', SubscriptionStatus::ENABLE);
        }])->whereHas('plans', function ($query) {
            $query->where('status', SubscriptionStatus::ENABLE); // Ensure category has at least one enabled plan
        })->get();

        return $this->successResponse(data: [
            'plans' => $subscriptionCategories,
        ]);
    }

    public function subscribe(object $request, SubscriptionPlan $plan): JsonResponse
    {
        $plan->load('category');
        $duration = $this->getDuration($request->query('duration'));

        $amountPaid = $plan->price * $duration;

        if ($amountPaid > $request->user()->wallet) {
            return $this->errorResponse(__('responses.insufficientFunds'));
        }

        try {
            DB::beginTransaction();

            $expiryDate = Carbon::now()->addMonth($duration)->toDateTimeString();
            $request->user()->update(['wallet' => $request->user()->wallet - $amountPaid]);
            $request->user()->subscriptions()->detach();
            $plan->user()->attach($request->user(), ['expires_on' => $expiryDate]);

            DB::commit();

            notify($request->user(), 'SUBSCRIPTION',[
                'plan' => $plan->name,
                'price' => $plan->price,
                'expiry' => $expiryDate,
            ], ['email']);
            //$request->user()->notify(new SubscriptionNotice($request->user(), $plan, $expiryDate));

            return $this->successResponse(__('responses.planSubscribed', ['name' => $plan->name.' '.$plan->category?->name]));
        } catch (\Throwable $th) {
            Log::error($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }

    private function getDuration(?int $duration): int
    {
        return in_array($duration, TransactionDuration::values()) ? $duration : 1;
    }

    public function unsubscribe(object $request, SubscriptionPlan $plan): JsonResponse
    {
        try {
            DB::beginTransaction();

            $plan->user()->detach($request->user());
            $request->user()->subscriptions()->attach(SubscriptionPlan::where('is_default', SubscriptionStatus::ENABLE)->first());

            DB::commit();

//            $request->user()->notify(new UnsubscriptionNotice($request->user(), $plan));
            notify($request->user(), 'UNSUBSCRIBE',[
                'plan' => $plan->name,
            ], ['email']);
            return $this->successResponse(__('responses.planUnsubscribed', ['name' => $plan->name]));
        } catch (\Throwable $th) {
            Log::error($th);

            return $this->errorResponse(__('responses.unknownError'));
        }
    }
}
