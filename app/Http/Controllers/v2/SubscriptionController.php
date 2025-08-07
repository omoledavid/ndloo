<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\NdFeature;
use App\Models\NdPlan;
use App\Services\SubscriptionService;
use App\Support\Services\BaseService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends BaseService
{
    use ApiResponses;
    public function plans(): JsonResponse
    {
        $plans = NdPlan::query()->with('features')->get();
        return $this->ok('Subscription plans fetched successfully', PlanResource::collection($plans));
    }
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:nd_plans,id',
        ]);
        if (auth()->user()->wallet < NdPlan::find($request->plan_id)->price) {
            return $this->error('Insufficient funds');
        }

        $subscriptionService = new SubscriptionService();
        $subscription = $subscriptionService->subscribe(auth()->user(), NdPlan::find($request->plan_id));
        return $this->ok('Subscription created successfully', new SubscriptionResource($subscription));
    }
    public function subscription()
    {
        $subscription = auth()->user()->activeSubscription()->first();
        if (!$subscription) {
            return $this->ok('No active subscription found');
        }
        return $this->ok('Subscription fetched successfully', new SubscriptionResource($subscription));
    }
    public function features()
    {
        return $this->ok('success', FeatureResource::collection(NdFeature::all()));
    }
    public function canUseFeature(Request $request)
    {
        $request->validate([
            'feature_name' => 'required|string',
        ]);
        $subscriptionService = new SubscriptionService();
        return $this->ok('success', $subscriptionService->canUseFeature(auth()->user(), $request->feature_name));
    }
}
