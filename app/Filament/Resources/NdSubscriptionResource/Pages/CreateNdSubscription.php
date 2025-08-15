<?php

namespace App\Filament\Resources\NdSubscriptionResource\Pages;

use App\Filament\Resources\NdSubscriptionResource;
use App\Models\NdPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateNdSubscription extends CreateRecord
{
    protected static string $resource = NdSubscriptionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Get the user and plan
        $user = User::findOrFail($data['user_id']);
        $plan = NdPlan::findOrFail($data['plan_id']);
        
        // Use the SubscriptionService to properly subscribe the user
        $subscriptionService = app(SubscriptionService::class);
        
        // The service will handle:
        // - Checking for existing subscriptions
        // - Ending current subscription if different plan
        // - Creating new subscription with proper dates
        // - Initializing feature usage
        $subscription = $subscriptionService->subscribe($user, $plan);
        
        return $subscription;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
