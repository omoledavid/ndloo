<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\NdPlan;
use App\Services\SubscriptionService;

class SubscriptionHelper
{
    /**
     * Subscribe a user to a plan using the SubscriptionService
     * 
     * @param User $user The user to subscribe
     * @param NdPlan $plan The plan to subscribe to
     * @return \App\Models\NdSubscription
     */
    public static function subscribeUser(User $user, NdPlan $plan)
    {
        $subscriptionService = app(SubscriptionService::class);
        return $subscriptionService->subscribe($user, $plan);
    }

    /**
     * Subscribe multiple users to the same plan
     * 
     * @param array $userIds Array of user IDs
     * @param NdPlan $plan The plan to subscribe to
     * @return array Results of subscription attempts
     */
    public static function subscribeMultipleUsers(array $userIds, NdPlan $plan)
    {
        $subscriptionService = app(SubscriptionService::class);
        $results = [];
        
        foreach ($userIds as $userId) {
            try {
                $user = User::find($userId);
                if ($user) {
                    $subscription = $subscriptionService->subscribe($user, $plan);
                    $results[] = [
                        'user_id' => $userId,
                        'user_name' => $user->fullname,
                        'success' => true,
                        'subscription_id' => $subscription->id,
                        'message' => "Successfully subscribed to {$plan->name}"
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'user_id' => $userId,
                    'user_name' => $user->fullname ?? 'Unknown',
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Check if a user can use a specific feature
     * 
     * @param User $user The user to check
     * @param string $featureKey The feature key to check
     * @return bool
     */
    public static function canUseFeature(User $user, string $featureKey)
    {
        $subscriptionService = app(SubscriptionService::class);
        return $subscriptionService->canUseFeature($user, $featureKey);
    }

    /**
     * Consume a feature for a user
     * 
     * @param User $user The user consuming the feature
     * @param string $featureKey The feature key to consume
     * @return void
     * @throws \Exception If feature limit reached
     */
    public static function consumeFeature(User $user, string $featureKey)
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->consumeFeature($user, $featureKey);
    }

    /**
     * Unsubscribe a user from their current plan
     * 
     * @param User $user The user to unsubscribe
     * @return void
     */
    public static function unsubscribeUser(User $user)
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->unsubscribe($user);
    }
}
