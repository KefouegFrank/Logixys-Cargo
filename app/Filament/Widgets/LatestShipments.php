<?php

namespace App\Filament\Widgets;

use App\Enums\ShipmentStatus;
use App\Filament\Resources\Shipments\ShipmentResource;
use App\Models\Shipment;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestShipments extends TableWidget
{
    protected static ?string $heading = 'Dernières expéditions';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(ShipmentResource::getEloquentQuery()->latest()->limit(8))
            ->paginated(false)
            ->recordUrl(fn (Shipment $record) => ShipmentResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('tracking_number')->label('Tracking #')->copyable(),
                TextColumn::make('receiver_name')->label('Destinataire'),
                TextColumn::make('shipper_city')
                    ->label('Trajet')
                    ->formatStateUsing(fn (Shipment $record) => "{$record->shipper_city} → {$record->receiver_city}"),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (Shipment $record) => match ($record->status) {
                        ShipmentStatus::Delivered => 'success',
                        ShipmentStatus::OnHold, ShipmentStatus::Cancelled => 'danger',
                        ShipmentStatus::Returned => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('expected_delivery_date')->label('Livraison prévue')->date(),
                TextColumn::make('created_at')->label('Créée')->since(),
            ])
            ->headerActions([
                Action::make('all')
                    ->label('Voir tout')
                    ->link()
                    ->url(ShipmentResource::getUrl('index')),
            ]);
    }
}
