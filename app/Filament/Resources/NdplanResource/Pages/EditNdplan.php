<?php

namespace App\Filament\Resources\NdplanResource\Pages;

use App\Filament\Resources\NdplanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNdplan extends EditRecord
{
    protected static string $resource = NdplanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Plans')
                ->url(route('filament.admin.resources.plans.index'))
                ->color('secondary'),
        ];
    }
}
