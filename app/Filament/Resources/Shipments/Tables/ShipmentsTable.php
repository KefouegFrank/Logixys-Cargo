<?php

namespace App\Filament\Resources\Shipments\Tables;

use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Filament\Resources\Shipments\Actions\InvoiceAction;
use App\Filament\Resources\Shipments\Actions\WaybillAction;
use App\Filament\Resources\Shipments\ShipmentResource;
use App\Models\Shipment;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_number')
                    ->label('Numéro de suivi')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Numéro copié')
                    ->weight('bold')
                    ->url(fn (Shipment $record) => ShipmentResource::getUrl('edit', ['record' => $record])),

                TextColumn::make('service_type')
                    ->label('Catégorie')
                    ->badge()
                    ->sortable(),

                TextColumn::make('shipper_name')
                    ->label('Nom de l\'expéditeur')
                    ->description(fn (Shipment $record) => $record->shipper_city ?: $record->origin_label)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('receiver_name')
                    ->label('Nom du destinataire')
                    ->description(fn (Shipment $record) => $record->receiver_company ?: ($record->receiver_city ?: $record->destination_label))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where(
                        fn (Builder $q) => $q
                            ->where('receiver_name', 'like', "%{$search}%")
                            ->orWhere('receiver_company', 'like', "%{$search}%")
                    ))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (Shipment $record) => match ($record->status) {
                        ShipmentStatus::Delivered => 'success',
                        ShipmentStatus::OnHold, ShipmentStatus::Cancelled => 'danger',
                        ShipmentStatus::Returned => 'warning',
                        default => 'info',
                    })
                    ->sortable(),

                // Off by default.
                TextColumn::make('creator.name')
                    ->label('Créé par')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->emptyStateHeading('Aucune expédition')
            ->emptyStateDescription('Créez une expédition avec « Ajouter un nouvel envoi ».')
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(ShipmentStatus::class),
                SelectFilter::make('service_type')
                    ->label('Catégorie')
                    ->options(ServiceType::class),
                SelectFilter::make('shipper_name')
                    ->label('Expéditeur')
                    ->options(fn () => self::distinct('shipper_name'))
                    ->searchable(),
                SelectFilter::make('receiver_name')
                    ->label('Destinataire')
                    ->options(fn () => self::distinct('receiver_name'))
                    ->searchable(),
                Filter::make('created_between')
                    ->label('Date')
                    ->schema([
                        DatePicker::make('created_from')->label('Du'),
                        DatePicker::make('created_until')->label('Au'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['created_from'] ?? null, fn (Builder $q, string $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['created_until'] ?? null, fn (Builder $q, string $date) => $q->whereDate('created_at', '<=', $date))),
            ])
            // Grouped: three separate buttons overflowed the row and clipped at the edge.
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->label('Modifier'),
                    InvoiceAction::make(),
                    WaybillAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** @return array<string, string> */
    private static function distinct(string $column): array
    {
        return Shipment::query()
            ->whereNotNull($column)
            ->distinct()
            ->orderBy($column)
            ->limit(200)
            ->pluck($column, $column)
            ->all();
    }
}
