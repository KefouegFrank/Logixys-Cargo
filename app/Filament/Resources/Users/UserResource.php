<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Équipe';

    protected static ?string $modelLabel = 'utilisateur';

    protected static ?string $pluralModelLabel = 'Équipe';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Nom')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true)->maxLength(255),
                Select::make('role')->label('Rôle')->options(UserRole::class)->required(),
                Select::make('locale')
                    ->label('Langue')
                    ->options(fn () => collect(config('locales'))->map(fn (array $l) => $l['native'])->all())
                    ->default(config('app.locale'))
                    ->required(),
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation) => $operation === 'create')
                    // Leaving it blank on edit must not blank the stored hash.
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->maxLength(255),
                Toggle::make('is_active')->label('Compte actif')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->color(fn (User $record) => $record->role === UserRole::Admin ? 'primary' : 'info'),
                IconColumn::make('is_active')->label('Actif')->boolean(),
                TextColumn::make('created_at')->label('Créé le')->date()->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('role')->label('Rôle')->options(UserRole::class),
            ])
            ->headerActions([
                CreateAction::make()->label('Nouvel utilisateur'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
        ];
    }
}
