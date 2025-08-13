<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Contracts\Enums\UserStates;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Widgets\UsersOverview;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
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
    public function getHeaderWidgets(): array
    {
        return [
            UsersOverview::class
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(User::count()),

            'Active' => Tab::make('Active')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', UserStates::ACTIVE);
                })
                ->icon('heroicon-o-check-badge')
                ->badge(User::where('status', UserStates::ACTIVE)->count()),

            'Inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', UserStates::INACTIVE);
                })
                ->icon('heroicon-o-clock')
                ->badge(User::where('status', UserStates::INACTIVE)->count()),

            'banned' => Tab::make('Banned')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', UserStates::SUSPENDED);
                })
                ->icon('heroicon-o-trash')
                ->badge(User::where('status', UserStates::SUSPENDED)->count()),
            'admin' => Tab::make('Admins')
                ->modifyQueryUsing(function ($query) {
                    $query->where('is_admin', true);
                })
                ->icon('heroicon-o-shield-check')
                ->badge(User::where('is_admin', true)->count()),
        ];

    }
}
