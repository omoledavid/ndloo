<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('firstname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('lastname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('age')
                    ->required()
                    ->numeric()
                    ->default(18),
                Forms\Components\TextInput::make('wallet')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('credits')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('gender')
                    ->required()
                    ->maxLength(255)
                    ->default('male'),
                Forms\Components\TextInput::make('longitude')
                    ->maxLength(255),
                Forms\Components\TextInput::make('latitude')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('dob'),
                Forms\Components\TextInput::make('language')
                    ->required()
                    ->maxLength(255)
                    ->default('en'),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required(),
                Forms\Components\TextInput::make('avatar')
                    ->required()
                    ->maxLength(255)
                    ->default('storage/avatars/default.png'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255),
                Forms\Components\Textarea::make('token')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('active')
                    ->required(),
                Forms\Components\Toggle::make('pushNotice')
                    ->required(),
                Forms\Components\Toggle::make('is_admin')
                    ->required(),
                Forms\Components\Toggle::make('is_online')
                    ->required(),
                Forms\Components\DateTimePicker::make('last_seen_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('profile')
                    ->label('User')
                    ->view('filament.tables.columns.user-profile'),
                Tables\Columns\TextColumn::make('wallet')
                    ->label('Wallet Balance')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state . ' USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dob')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('pushNotice')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_seen_at')
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
                Tables\Columns\TextColumn::make('deleted_at')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
