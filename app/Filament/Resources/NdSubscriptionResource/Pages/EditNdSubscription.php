<?php

namespace App\Filament\Resources\NdSubscriptionResource\Pages;

use App\Filament\Resources\NdSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNdSubscription extends EditRecord
{
    protected static string $resource = NdSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
