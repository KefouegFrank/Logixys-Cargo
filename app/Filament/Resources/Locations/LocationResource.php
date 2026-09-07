<?php

namespace App\Filament\Resources\Locations;

use App\Enums\LocationType;
use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Models\Location;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Référentiel';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Lieux';

    protected static ?string $modelLabel = 'lieu';

    protected static ?string $pluralModelLabel = 'Lieux';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nom')->required()->maxLength(150),
            Select::make('type')->label('Type')->options(LocationType::class)->default(LocationType::City)->required(),
            TextInput::make('city')->label('Ville')->maxLength(120),
            TextInput::make('country')->label('Pays')->default('FR')->maxLength(2),
            TextInput::make('lat')
                ->label('Latitude')
                ->numeric()
                ->helperText('Laissez vide pour laisser le géocodage renseigner l\'expédition.'),
            TextInput::make('lng')->label('Longitude')->numeric(),
            Toggle::make('is_active')->label('Actif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('type')->label('Type')->badge(),
                TextColumn::make('city')->label('Ville')->searchable(),
                TextColumn::make('country')->label('Pays'),
                TextColumn::make('lat')->label('Latitude')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lng')->label('Longitude')->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')->label('Actif')->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')->label('Type')->options(LocationType::class),
                TernaryFilter::make('is_active')->label('Actif'),
            ])
            ->headerActions([CreateAction::make()->label('Nouveau lieu')])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return ['index' => ListLocations::route('/')];
    }
}
