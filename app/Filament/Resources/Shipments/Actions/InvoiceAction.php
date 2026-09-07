<?php

namespace App\Filament\Resources\Shipments\Actions;

use App\Models\Shipment;
use App\Services\Documents\ReceiptIssuer;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceAction
{
    public static function make(): Action
    {
        return Action::make('facture')
            ->label('Facture')
            ->icon(Heroicon::OutlinedDocumentText)
            ->color('gray')
            ->action(function (Shipment $record): StreamedResponse {
                $receipt = app(ReceiptIssuer::class)->issue($record);

                return response()->streamDownload(
                    fn () => print (Storage::disk('local')->get($receipt->path)),
                    "{$receipt->number}.pdf",
                    ['Content-Type' => 'application/pdf'],
                );
            });
    }
}
