<?php

namespace App\Filament\Resources\NdplanResource\Pages;

use App\Filament\Resources\NdplanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNdplans extends ListRecords
{
    protected static string $resource = NdplanResource::class;
    protected static ?string $title = 'Plans';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()->label('Add Plan'),
        ];
    }
}
