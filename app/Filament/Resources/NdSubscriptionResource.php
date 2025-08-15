<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NdSubscriptionResource\Pages;
use App\Filament\Resources\NdSubscriptionResource\RelationManagers;
use App\Models\NdSubscription;
use App\Models\NdPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\BulkAction;

class NdSubscriptionResource extends Resource
{
    protected static ?string $model = NdSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $label = 'Subscriptions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->hint('Select the user to subscribe')
                    ->searchable()
                    ->options(function () {
                        // Preset 3 first users by fullname
                        return \App\Models\User::query()
                            ->orderBy('id')
                            ->limit(3)
                            ->get()
                            ->pluck('fullname', 'id');
                    })
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\User::query()
                            ->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->limit(20)
                            ->get()
                            ->pluck('fullname', 'id');
                    })
                    ->getOptionLabelUsing(fn ($value): ?string => \App\Models\User::find($value)?->fullname)
                    ->required()
                    ->helperText('The user will be automatically subscribed to the selected plan. Any existing subscription will be replaced.'),
                
                Forms\Components\Select::make('plan_id')
                    ->label('Subscription Plan')
                    ->hint('Select the plan to subscribe the user to')
                    ->relationship('plan', 'name')
                    ->required()
                    ->helperText('The subscription will start immediately and use the plan\'s duration settings.'),
                
                Forms\Components\Section::make('Subscription Details')
                    ->description('These details will be automatically managed by the system')
                    ->schema([
                        Forms\Components\Placeholder::make('subscription_info')
                            ->label('How it works')
                            ->content('When you create this subscription, the system will:
                            • Check if the user has an existing subscription
                            • End the current subscription if it\'s a different plan
                            • Start the new subscription immediately
                            • Set the end date based on the plan\'s duration
                            • Initialize feature usage tracking for the user')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.fullname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('subscribeToPlan')
                    ->label('Subscribe to Plan')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('plan_id')
                            ->label('New Subscription Plan')
                            ->options(NdPlan::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->helperText('Select the new plan to subscribe this user to.'),
                    ])
                    ->action(function (array $data, NdSubscription $record) {
                        $plan = NdPlan::findOrFail($data['plan_id']);
                        $user = User::find($record->user_id);
                        
                        if ($user) {
                            $subscriptionService = app(SubscriptionService::class);
                            $subscriptionService->subscribe($user, $plan);
                            
                            \Filament\Notifications\Notification::make()
                                ->title("Successfully subscribed {$user->fullname} to {$plan->name}")
                                ->success()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Subscribe User to New Plan')
                    ->modalDescription('This will replace the current subscription with the new plan.')
                    ->modalSubmitActionLabel('Subscribe User'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                BulkAction::make('subscribeUsers')
                    ->label('Subscribe Users to Plan')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('plan_id')
                            ->label('Subscription Plan')
                            ->options(NdPlan::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->helperText('Select the plan to subscribe all selected users to.'),
                    ])
                    ->action(function (array $data, array $records) {
                        $plan = NdPlan::findOrFail($data['plan_id']);
                        $subscriptionService = app(SubscriptionService::class);
                        
                        $successCount = 0;
                        $errors = [];
                        
                        foreach ($records as $record) {
                            try {
                                $user = User::find($record->user_id);
                                if ($user) {
                                    $subscriptionService->subscribe($user, $plan);
                                    $successCount++;
                                }
                            } catch (\Exception $e) {
                                $errors[] = "User {$user->fullname}: " . $e->getMessage();
                            }
                        }
                        
                        if ($successCount > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title("Successfully subscribed {$successCount} users to {$plan->name}")
                                ->success()
                                ->send();
                        }
                        
                        if (!empty($errors)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Some subscriptions failed')
                                ->body(implode("\n", $errors))
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Subscribe Users to Plan')
                    ->modalDescription('This will subscribe all selected users to the chosen plan. Any existing subscriptions will be replaced.')
                    ->modalSubmitActionLabel('Subscribe Users'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNdSubscriptions::route('/'),
            'create' => Pages\CreateNdSubscription::route('/create'),
            'edit' => Pages\EditNdSubscription::route('/{record}/edit'),
        ];
    }
}
