<?php

namespace App\Filament\Widgets;

use App\Enums\ShipmentStatus;
use App\Filament\Resources\Shipments\ShipmentResource;
use App\Models\Shipment;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * On hold, returned, or past their expected delivery date — the shipments an agent
 * needs to look at today, not just whatever was created most recently.
 */
class ShipmentsNeedingAttention extends TableWidget
{
    protected static ?string $heading = 'Expéditions à traiter';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ShipmentResource::getEloquentQuery()
                    ->where(fn ($query) => $query
                        ->whereIn('status', [ShipmentStatus::OnHold, ShipmentStatus::Returned])
                        ->orWhere(fn ($query) => $query->overdue()))
                    ->orderByRaw("expected_delivery_date IS NULL, expected_delivery_date asc")
            )
            ->paginated(false)
            ->recordUrl(fn (Shipment $record) => ShipmentResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('tracking_number')->label('Numéro de suivi')->copyable(),
                TextColumn::make('receiver_name')->label('Destinataire'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (Shipment $record) => match ($record->status) {
                        ShipmentStatus::OnHold, ShipmentStatus::Cancelled => 'danger',
                        ShipmentStatus::Returned => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('expected_delivery_date')
                    ->label('Livraison prévue')
                    ->date('d/m/Y')
                    ->color(fn (Shipment $record) => $record->expected_delivery_date?->isPast() ? 'danger' : null)
                    ->description(fn (Shipment $record) => $record->expected_delivery_date?->isPast()
                        ? $record->expected_delivery_date->diffForHumans(['parts' => 1]).' de retard'
                        : null),
                TextColumn::make('created_at')->label('Créée')->since(),
            ])
            ->emptyStateHeading('Rien à traiter')
            ->emptyStateDescription('Aucune expédition bloquée ou en retard.')
            ->headerActions([
                Action::make('all')
                    ->label('Voir toutes les expéditions')
                    ->link()
                    ->url(ShipmentResource::getUrl('index')),
            ]);
    }
}
