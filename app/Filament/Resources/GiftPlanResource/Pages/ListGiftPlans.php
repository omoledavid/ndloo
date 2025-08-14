<?php

namespace App\Filament\Resources\GiftPlanResource\Pages;

use App\Filament\Resources\GiftPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGiftPlans extends ListRecords
{
    protected static string $resource = GiftPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
