<?php

namespace App\Filament\Resources\Shipments\Actions;

use App\Enums\ShipmentStatus;
use App\Filament\Forms\Components\LocationPinField;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Services\Geocoding\GeocodingService;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateStatusAction
{
    public static function make(): Action
    {
        return Action::make('updateStatus')
            ->label('Update status')
            ->icon(Heroicon::OutlinedMapPin)
            ->modalHeading('Update shipment status')
            ->modalSubmitActionLabel('Update')
            ->schema(fn (Shipment $record) => [
                Select::make('status')
                    ->options(ShipmentStatus::class)
                    ->default($record->status)
                    ->required(),
                TextInput::make('location_label')
                    ->label('Location')
                    ->datalist(fn () => ShipmentEvent::query()
                        ->whereNotNull('location_label')
                        ->distinct()
                        ->orderBy('location_label')
                        ->pluck('location_label')
                        ->all())
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        if (! filled($state)) {
                            return;
                        }

                        $coords = app(GeocodingService::class)->geocode($state);

                        if ($coords !== null) {
                            $set('location_position', ['lat' => $coords['lat'], 'lng' => $coords['lng'], 'isManual' => false]);
                        }
                    }),
                LocationPinField::make('location_position')
                    ->label('Pin')
                    ->live(),
                DateTimePicker::make('occurred_at')
                    ->label('Date and time')
                    ->default(now())
                    ->required(),
                Textarea::make('remarks'),
                Toggle::make('is_public')
                    ->label('Visible to customer')
                    ->default(true),
                Checkbox::make('notify_parties')
                    ->label('Notify shipper and receiver')
                    ->default(true)
                    ->helperText('Email sending is not wired up yet; this is recorded for later.'),
            ])
            ->action(function (Shipment $record, array $data) {
                // Select::options(ShipmentStatus::class) already casts this back to the enum.
                $status = $data['status'];

                $position = $data['location_position'] ?? [];

                DB::transaction(function () use ($record, $data, $status, $position) {
                    $record->events()->create([
                        'status' => $status,
                        'location_label' => $data['location_label'],
                        'location_lat' => $position['lat'] ?? null,
                        'location_lng' => $position['lng'] ?? null,
                        'is_manual_position' => $position['isManual'] ?? false,
                        'occurred_at' => $data['occurred_at'],
                        'remarks' => $data['remarks'] ?? null,
                        'is_public' => $data['is_public'],
                        'created_by' => Auth::id(),
                    ]);

                    $record->status = $status;

                    if ($status === ShipmentStatus::Delivered) {
                        $record->delivered_at = now();
                    }

                    $record->save();
                });

                // notify_parties queues shipper/receiver emails once step 5 (Email) lands.
            });
    }
}
