<?php

namespace App\Filament\Resources\Shipments\RelationManagers;

use App\Enums\ShipmentStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $title = 'Historique des expéditions';

    public function table(Table $table): Table
    {
        // Read-only by design: ShipmentEvent blocks updates and deletes, and new rows come
        // from the "Mettre à jour le statut" action so the shipment status moves with them.
        return $table
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                TextColumn::make('occurred_at')->label('Date')->date('d/m/Y')->sortable(),
                TextColumn::make('occurred_at_time')
                    ->label('Heure')
                    ->getStateUsing(fn ($record) => $record->occurred_at?->format('H:i')),
                TextColumn::make('location_label')->label('Emplacement')->limit(40),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (ShipmentStatus $state) => $state->label()),
                TextColumn::make('creator.name')->label('Mis à jour par'),
                TextColumn::make('remarks')->label('Remarques')->limit(50)->wrap(),
                IconColumn::make('is_public')->label('Public')->boolean(),
            ])
            ->emptyStateHeading('Aucun événement')
            ->emptyStateDescription('Utilisez « Mettre à jour le statut » pour ajouter une étape.');
    }
}
