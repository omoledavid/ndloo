<?php 

namespace App\Services;

use App\Models\NdFeature;
use App\Models\NdFeatureUsuage;
use App\Models\NdPlan;
use App\Models\NdSubscription;
use App\Models\User;
use Illuminate\Support\Carbon;

class SubscriptionService
{
    public function plans()
    {
        return NdPlan::all();
    }

    public function subscribe(User $user, NdPlan $plan): NdSubscription
    {
        // Check if user already has an active subscription
        $existingSubscription = $user->activeSubscription();

        // If user has an active subscription
        if ($existingSubscription) {
            // If the plan is the same, prevent subscribing again
            if ($existingSubscription->plan_id == $plan->id && $existingSubscription->isActive()) {
                return $existingSubscription;
            }

            // Otherwise, end the current subscription immediately and clean up feature usage
            $existingSubscription->update([
                'ends_at' => now(),
            ]);
            NdFeatureUsuage::where('subscription_id', $existingSubscription->id)->delete();
        }

        // Create new subscription
        $subscription = NdSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration_days),
        ]);

        // Initialize feature usage
        foreach ($plan->features as $feature) {
            NdFeatureUsuage::create([
                'subscription_id' => $subscription->id,
                'feature_id' => $feature->id,
                'used' => 0,
            ]);
        }

        return $subscription;
    }
    public function unsubscribe(User $user): void
    {
        $subscription = $user->activeSubscription();

        if ($subscription) {
            $subscription->update([
                'ends_at' => now(), // Ends immediately
            ]);

            // Optional: Clean up usage records
            NdFeatureUsuage::where('subscription_id', $subscription->id)->delete();
        }
    }

    public function hasFeature(User $user, string $featureKey): bool
    {
        $subscription = $user->activeSubscription()->first();

        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        return $subscription->plan->features->contains('name', $featureKey);
    }

    public function canUseFeature(User $user, string $featureKey): bool
    {
        $subscription = $user->activeSubscription()->first();
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $feature = NdFeature::where('name', $featureKey)->first();
        if (!$feature) return false;

        $planFeature = $subscription->plan->features()
            ->where('nd_features.id', $feature->id)
            ->first();

        $usage = NdFeatureUsuage::where('subscription_id', $subscription->id)
            ->where('feature_id', $feature->id)
            ->first();

        return $usage && $planFeature && $usage->used < $planFeature->pivot->limit;
    }

    public function consumeFeature(User $user, string $featureKey): void
    {
        if (!$this->canUseFeature($user, $featureKey)) {
            throw new \Exception('Feature limit reached.');
        }

        $feature = NdFeature::where('name', $featureKey)->firstOrFail();
        $subscription = $user->activeSubscription()->first();

        NdFeatureUsuage::where('subscription_id', $subscription->id)
            ->where('feature_id', $feature->id)
            ->increment('used');
    }
}
