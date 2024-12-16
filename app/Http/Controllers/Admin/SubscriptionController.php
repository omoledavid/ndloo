<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Support\Services\BaseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SubscriptionController extends BaseService
{
//    public function __construct(private readonly SubscriptionService $subscriptionService) {}

//    public function plans(): View
//    {
//        return $this->subscriptionService->planView();
//    }
    public function getSubscriptions()
    {
        return $this->successResponse(data: [
            'subscriptions' => SubscriptionPlan::all()
        ]);
    }
    public function getSubscription(SubscriptionPlan $subscription)
    {
        return $this->successResponse(data: [
            'subscription' => $subscription
        ]);
    }
    public function editSubscription(SubscriptionPlan $subscription, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'message_count' => 'required',
            'calls' => 'required',
            'gift_conversion' => 'required',
        ]);
        $subscription->update([
            'name' => $request->name,
            'price' => $request->price,
            'message_count' => $request->message_count,
            'calls' => $request->calls === "on",
            "gift_conversion" => $request->gift === "on"
        ]);
        return $this->successResponse(message:'Subscription updated successfully',data: [
            'subscription' => $subscription
        ]);
    }
    public function createSubscription(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category' => 'required',
            'message_count' => 'required|numeric',
            'calls' => 'required',
            'gift_conversion' => 'required',
        ]);

        if ($subscription =SubscriptionPlan::create([
            'subscription_category_id' => $request->category,
            'name' => $request->name,
            'price' => $request->price,
            'message_count' => $request->message_count,
            'calls' => $request->calls === "on",
            "gift_conversion" => $request->gift === "on"

        ])) {
            return $this->successResponse('Message created successfully',data:[
                'subscription' => $subscription
            ]);
        }

        return $this->errorResponse(message:'Subscription not created');
    }
}
