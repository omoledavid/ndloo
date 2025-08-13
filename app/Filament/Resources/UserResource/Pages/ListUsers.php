<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(User::count()),

            'Active' => Tab::make('Active')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', UserStatus::ACTIVE);
                })
                ->icon('heroicon-o-check-badge')
                ->badge(User::where('status', UserStatus::ACTIVE)->count()),

            'Inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', UserStatus::INACTIVE);
                })
                ->icon('heroicon-o-clock')
                ->badge(User::where('status', UserStatus::INACTIVE)->count()),

            'banned' => Tab::make('Banned')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', UserStatus::BLOCKED);
                })
                ->icon('heroicon-o-trash')
                ->badge(User::where('status', UserStatus::BLOCKED)->count()),
            'admin' => Tab::make('Admins')
                ->modifyQueryUsing(function ($query) {
                    $query->where('is_admin', true);
                })
                ->icon('heroicon-o-shield-check')
                ->badge(User::where('is_admin', true)->count()),
        ];

    }
}
