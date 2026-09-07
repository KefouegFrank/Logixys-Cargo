<?php

namespace App\Services\Documents;

use App\Models\Receipt;
use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfRenderer
{
    public function invoice(Shipment $shipment, Receipt $receipt): string
    {
        return $this->render('pdf.invoice', [
            'shipment' => $shipment->loadMissing('packages'),
            'receipt' => $receipt,
        ]);
    }

    public function waybill(Shipment $shipment): string
    {
        return $this->render('pdf.waybill', [
            'shipment' => $shipment->loadMissing(['packages', 'carrier']),
            'landscape' => false,
        ]);
    }

    /** @param array<string, mixed> $data */
    private function render(string $view, array $data): string
    {
        return Pdf::loadView($view, $data)->setPaper('a4', $data['landscape'] ?? false ? 'landscape' : 'portrait')->output();
    }
}
