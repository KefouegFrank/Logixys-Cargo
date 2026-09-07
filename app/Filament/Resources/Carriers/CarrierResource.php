<?php

namespace App\Filament\Resources\Carriers;

use App\Filament\Resources\Carriers\Pages\ListCarriers;
use App\Models\Carrier;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CarrierResource extends Resource
{
    protected static ?string $model = Carrier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Référentiel';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Transporteurs';

    protected static ?string $modelLabel = 'transporteur';

    protected static ?string $pluralModelLabel = 'Transporteurs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nom')->required()->maxLength(120),
            TextInput::make('code')->label('Code')->maxLength(20)->unique(ignoreRecord: true),
            TextInput::make('contact_email')->label('E-mail')->email()->maxLength(150),
            TextInput::make('contact_phone')->label('Téléphone')->tel()->maxLength(40),
            Toggle::make('is_active')->label('Actif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('code')->label('Code')->badge(),
                TextColumn::make('contact_email')->label('E-mail')->copyable(),
                TextColumn::make('contact_phone')->label('Téléphone'),
                TextColumn::make('shipments_count')->label('Expéditions')->counts('shipments')->sortable(),
                IconColumn::make('is_active')->label('Actif')->boolean(),
            ])
            ->filters([TernaryFilter::make('is_active')->label('Actif')])
            ->headerActions([CreateAction::make()->label('Nouveau transporteur')])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return ['index' => ListCarriers::route('/')];
    }
}
