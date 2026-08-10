<?php

namespace App\Filament\Resources\Shipments\Tables;

use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Filament\Resources\Shipments\Actions\UpdateStatusAction;
use App\Models\Shipment;
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
                    ->label('Tracking #')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('receiver_name')
                    ->label('Receiver')
                    ->formatStateUsing(fn (Shipment $record) => $record->receiver_company
                        ? "{$record->receiver_name} ({$record->receiver_company})"
                        : $record->receiver_name)
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where(fn (Builder $q) => $q
                            ->where('receiver_name', 'like', "%{$search}%")
                            ->orWhere('receiver_company', 'like', "%{$search}%"));
                    }),
                TextColumn::make('shipper_city')
                    ->label('Route')
                    ->formatStateUsing(fn (Shipment $record) => "{$record->shipper_city} → {$record->receiver_city}"),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (Shipment $record) => match ($record->status) {
                        ShipmentStatus::Delivered => 'success',
                        ShipmentStatus::OnHold, ShipmentStatus::Cancelled => 'danger',
                        ShipmentStatus::Returned => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(ShipmentStatus::class),
                SelectFilter::make('service_type')->options(ServiceType::class),
                Filter::make('created_between')
                    ->schema([
                        DatePicker::make('created_from')->label('Created from'),
                        DatePicker::make('created_until')->label('Created until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn (Builder $q, string $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn (Builder $q, string $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                UpdateStatusAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
