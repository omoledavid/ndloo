<?php

namespace App\Filament\Resources\NdplanResource\RelationManagers;

use App\Models\NdFeature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FeaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'features';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pivot.limit')
                    ->label('Usage Limit')
                    ->numeric()
                    ->required()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Description')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.limit')
                    ->label('Usage Limit')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->options(function () {
                                // Get the current plan's attached feature IDs
                                $attachedFeatureIds = $this->getOwnerRecord()
                                    ->features()
                                    ->pluck('nd_features.id')
                                    ->toArray();

                                // Only show features not already attached to this plan
                                return NdFeature::query()
                                    ->whereNotIn('id', $attachedFeatureIds)
                                    ->pluck('name', 'id');
                            }),
                        Forms\Components\TextInput::make('limit')
                            ->label('Usage Limit')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn (Tables\Actions\EditAction $action): array => [
                        Forms\Components\TextInput::make('pivot.limit')
                            ->label('Usage Limit')
                            ->numeric()
                            ->required(),
                    ]),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
