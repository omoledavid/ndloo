<?php

namespace App\Filament\Resources\NdSubscriptionResource\Widgets;

use App\Models\NdPlan;
use App\Models\NdSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionPlanStat extends BaseWidget
{
    protected function getStats(): array
    {
        // Get total subscription plans
        $totalSubscriptionPlans = NdPlan::count();
        
        // Get total subscribers (users with active subscriptions)
        $totalSubscribers = NdSubscription::count();

        return [
            Stat::make('Total Subscription Plans', $totalSubscriptionPlans)
                ->description('Available subscription plans')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-credit-card')
                ->chart(NdPlan::query()
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(7)
                    ->pluck('count')
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Total Subscribers', $totalSubscribers)
                ->description('Active subscription users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->icon('heroicon-o-users')
                ->chart(NdSubscription::query()
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(7)
                    ->pluck('count')
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

           
        ];
    }
}
