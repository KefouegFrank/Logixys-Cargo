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

    /** @return array<int, mixed> */
    public function getSidebarFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    // The schema renders these in the sidebar instead.
    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        [$this->eventData, $data] = ShipmentEventRecorder::split($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        app(ShipmentEventRecorder::class)->record($this->record, $this->eventData);
    }
}
