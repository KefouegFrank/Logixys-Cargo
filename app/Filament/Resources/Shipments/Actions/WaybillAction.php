<?php

namespace App\Filament\Resources\Shipments\Actions;

use App\Models\Shipment;
use App\Services\Documents\PdfRenderer;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WaybillAction
{
    public static function make(): Action
    {
        // Rendered fresh each time: unlike an invoice, a waybill has no issued state to keep.
        return Action::make('lettreDeTransport')
            ->label('Lettre de transport')
            ->icon(Heroicon::OutlinedTicket)
            ->color('gray')
            ->action(function (Shipment $record): StreamedResponse {
                $pdf = app(PdfRenderer::class)->waybill($record);

                return response()->streamDownload(
                    fn () => print ($pdf),
                    "lettre-de-transport-{$record->tracking_number}.pdf",
                    ['Content-Type' => 'application/pdf'],
                );
            });
    }
}
