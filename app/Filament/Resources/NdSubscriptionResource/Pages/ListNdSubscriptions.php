<?php

namespace App\Filament\Resources\NdSubscriptionResource\Pages;

use App\Filament\Resources\NdSubscriptionResource;
use App\Filament\Resources\NdSubscriptionResource\Widgets\SubscriptionPlanStat;
use App\Models\NdPlan;
use App\Models\NdSubscription;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListNdSubscriptions extends ListRecords
{
    protected static string $resource = NdSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getHeaderWidgets(): array
    {
        return [
            SubscriptionPlanStat::class,
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-o-rectangle-stack')
                ->badge(NdSubscription::count()),

            'Free' => Tab::make('Free')
                ->modifyQueryUsing(function ($query) {
                    $query->where('name', 'Free');
                })
                ->icon('heroicon-o-sparkles')
                ->badge(NdSubscription::whereHas('plan', function ($query) {
                    $query->where('name', 'Free');
                })->count()),

            'Silver' => Tab::make('Silver')
                ->modifyQueryUsing(function ($query) {
                    $query->whereHas('plan', function ($query) {
                        $query->where('name', 'Silver');
                    });
                })
                ->icon('heroicon-o-star')
                ->badge(NdSubscription::whereHas('plan', function ($query) {
                    $query->where('name', 'Silver');
                })->count()),

            'Gold' => Tab::make('Gold')
                ->modifyQueryUsing(function ($query) {
                    $query->where('name', 'Gold');
                })
                ->icon('heroicon-o-trophy')
                ->badge(NdSubscription::whereHas('plan', function ($query) {
                    $query->where('name', 'Gold');
                })->count()),
            'Platinum' => Tab::make('Platinum')
                ->modifyQueryUsing(function ($query) {
                    $query->where('name', 'Platinum');
                })
                ->icon('heroicon-o-fire')
                ->badge(NdSubscription::whereHas('plan', function ($query) {
                    $query->where('name', 'Platinum');
                })->count()),
        ];

    }
}
