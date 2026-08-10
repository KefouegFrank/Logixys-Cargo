<?php

namespace App\Filament\Resources\Shipments\RelationManagers;

use App\Enums\PackageType;
use App\Models\Package;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'packages';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('package_type')
                ->options(PackageType::class)
                ->required(),
            TextInput::make('quantity')
                ->numeric()
                ->default(1)
                ->required(),
            TextInput::make('description')
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('weight_kg')
                ->numeric()
                ->suffix('kg'),
            TextInput::make('length_cm')
                ->numeric()
                ->suffix('cm'),
            TextInput::make('width_cm')
                ->numeric()
                ->suffix('cm'),
            TextInput::make('height_cm')
                ->numeric()
                ->suffix('cm'),
            TextInput::make('unit_value')
                ->numeric()
                ->prefix('€')
                ->helperText('Declared value per unit; the row total is calculated automatically.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('package_type')->badge(),
                TextColumn::make('quantity')->numeric(),
                TextColumn::make('description')->limit(40),
                TextColumn::make('weight_kg')->numeric()->suffix(' kg'),
                TextColumn::make('chargeable_weight')
                    ->label('Chargeable weight')
                    ->getStateUsing(fn (Package $record) => number_format(
                        $record->chargeableWeightKg($this->getOwnerRecord()->service_type->volumetricDivisor()),
                        2
                    ).' kg'),
                TextColumn::make('unit_value')->numeric()->prefix('€'),
                TextColumn::make('amount')->numeric()->prefix('€'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
