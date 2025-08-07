<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NdplanResource\Pages;
use App\Filament\Resources\NdplanResource\RelationManagers;
use App\Models\Ndplan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NdplanResource extends Resource
{
    protected static ?string $model = Ndplan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Plans';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $slug = 'plans';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191)
                    ->disabled(fn ($record) => $record && $record->name === 'Free'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('duration_days')
                    ->numeric()
                    ->default(fn ($record) => $record && $record->name === 'Free' ? 'Forever' : null)
                    ->disabled(fn ($record) => $record && $record->name === 'Free'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state == null ? 'Forever' : $state),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FeaturesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNdplans::route('/'),
            'create' => Pages\CreateNdplan::route('/create'),
            'edit' => Pages\EditNdplan::route('/{record}/edit'),
        ];
    }
}
