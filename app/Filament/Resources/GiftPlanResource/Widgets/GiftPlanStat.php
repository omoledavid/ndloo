<?php

namespace App\Filament\Resources\GiftPlanResource\Widgets;

use App\Models\GiftPlan;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanUser;
use App\Models\UserGift;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GiftPlanStat extends BaseWidget
{
    protected function getStats(): array
    {
        // Get total subscription plans
        $totalSubscriptionPlans = SubscriptionPlan::count();
        
        // Get total subscribers (users with active subscriptions)
        $totalSubscribers = SubscriptionPlanUser::where('active', true)->count();
        
        // Get total gift plans
        $totalGiftPlans = GiftPlan::count();
        
        // Get total gifts sent
        $totalGiftsSent = UserGift::count();
        
        // Get total gift revenue (sum of all gift amounts)
        $totalGiftRevenue = UserGift::join('gift_plans', 'user_gifts.gift_plan_id', '=', 'gift_plans.id')
            ->sum('gift_plans.amount');
        
        // Get active gift plans
        $activeGiftPlans = GiftPlan::where('status', 1)->count();

        return [
            Stat::make('Total Subscription Plans', $totalSubscriptionPlans)
                ->description('Available subscription plans')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-credit-card')
                ->chart(SubscriptionPlan::query()
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
                ->chart(SubscriptionPlanUser::query()
                    ->where('active', true)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(7)
                    ->pluck('count')
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Total Gift Plans', $totalGiftPlans)
                ->description('Available gift options')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->icon('heroicon-o-gift')
                ->chart(GiftPlan::query()
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(7)
                    ->pluck('count')
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Total Gifts Sent', $totalGiftsSent)
                ->description('Gifts exchanged between users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->icon('heroicon-o-heart')
                ->chart(UserGift::query()
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(7)
                    ->pluck('count')
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Total Gift Revenue', '$' . number_format($totalGiftRevenue / 100, 2))
                ->description('Revenue from gift transactions')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-currency-dollar')
                ->chart(UserGift::query()
                    ->join('gift_plans', 'user_gifts.gift_plan_id', '=', 'gift_plans.id')
                    ->selectRaw('DATE(user_gifts.created_at) as date, SUM(gift_plans.amount) as total')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(7)
                    ->pluck('total')
                    ->map(fn($amount) => $amount / 100)
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Active Gift Plans', $activeGiftPlans)
                ->description('Currently available gifts')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->icon('heroicon-o-check-circle')
                ->chart(GiftPlan::query()
                    ->where('status', 1)
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
