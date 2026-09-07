<?php

namespace App\Filament\Resources\Shipments\Pages;

use App\Filament\Resources\Shipments\ShipmentResource;
use App\Services\ShipmentEventRecorder;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShipment extends EditRecord
{
    protected static string $resource = ShipmentResource::class;

    /** @var array<string, mixed> */
    protected array $eventData = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /** @return array<int, mixed> */
    public function getSidebarFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    // The schema renders these in the sidebar instead.
    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        [$this->eventData, $data] = ShipmentEventRecorder::split($data);

        return $data;
    }

    protected function afterSave(): void
    {
        if (app(ShipmentEventRecorder::class)->record($this->record, $this->eventData)) {
            // Cleared so the next save doesn't post the same event a second time.
            $this->form->fill([
                ...$this->form->getState(shouldCallHooks: false),
                'event_status' => null,
                'event_location' => null,
                'event_remarks' => null,
            ]);
        }
    }
}
