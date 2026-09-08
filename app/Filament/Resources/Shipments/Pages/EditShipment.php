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

    // Nothing in the topbar; Save/Delete/Cancel live in the footer below the form.
    // Facture and Lettre de transport stay on the shipments table row menu only.
    protected function getHeaderActions(): array
    {
        return [];
    }

    /** @return array<int, mixed> */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            DeleteAction::make(),
            $this->getCancelFormAction(),
        ];
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
                ...$this->form->getState(shouldCallHooksBefore: false),
                'event_status' => null,
                'event_location' => null,
                'event_remarks' => null,
            ]);
        }

        // Saved — the autosaved draft in localStorage has nothing left to protect.
        $this->dispatch('shipment-draft-saved');
    }
}
