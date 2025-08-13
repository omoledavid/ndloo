<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Contracts\Enums\UserStates;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('All Users', User::query()->count())
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('gray')
                ->icon('heroicon-o-users')
                ->chart(User::query()
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(8)
                    ->pluck('count')
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Active Users', User::query()->where('status', UserStates::ACTIVE)->count())
                ->description('Currently active users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-check-badge')
                ->chart(User::query()
                    ->where('status', UserStates::ACTIVE)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(8)
                    ->pluck('count')
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Inactive Users', User::query()->where('status', UserStates::INACTIVE)->count())
                ->description('Currently inactive users')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->chart(User::query()
                    ->where('status', UserStates::INACTIVE)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(8)
                    ->pluck('count')
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Banned Users', User::query()->where('status', UserStates::SUSPENDED)->count())
                ->description('Users with banned status')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->chart(User::query()
                    ->where('status', UserStates::SUSPENDED)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit(8)
                    ->pluck('count')
                    ->toArray())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
        ];
    }
}
