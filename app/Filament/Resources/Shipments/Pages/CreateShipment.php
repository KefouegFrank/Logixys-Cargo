<?php

namespace App\Filament\Resources\Shipments\Pages;

use App\Filament\Resources\Shipments\ShipmentResource;
use App\Services\ShipmentEventRecorder;
use Filament\Resources\Pages\CreateRecord;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    /** @var array<string, mixed> */
    protected array $eventData = [];

    public function getTitle(): string
    {
        return 'Ajouter un nouvel envoi';
    }

    // Create / Cancel render in Filament's default full-width footer, at the bottom
    // of the page — no override needed here.

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        [$this->eventData, $data] = ShipmentEventRecorder::split($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        app(ShipmentEventRecorder::class)->record($this->record, $this->eventData);

        // Saved — the autosaved draft in localStorage would otherwise resurface on the
        // next new shipment and offer to restore this one.
        $this->dispatch('shipment-draft-saved');
    }
}
