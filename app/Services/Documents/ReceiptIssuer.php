<?php

namespace App\Services\Documents;

use App\Models\Receipt;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReceiptIssuer
{
    public function __construct(private readonly PdfRenderer $pdf = new PdfRenderer) {}

    /**
     * Returns the shipment's invoice, issuing it on first request. Totals and the PDF are
     * frozen at that moment, so asking again hands back the file the customer already has.
     */
    public function issue(Shipment $shipment): Receipt
    {
        $existing = $shipment->receipts()->latest('id')->first();

        if ($existing !== null) {
            return $existing;
        }

        return DB::transaction(function () use ($shipment) {
            $receipt = Receipt::create([
                'shipment_id' => $shipment->id,
                'number' => $this->nextNumber(),
                'issued_at' => now(),
                'total_ht' => $shipment->total_ht,
                'tax_amount' => $shipment->tax_amount,
                'total_ttc' => $shipment->total_ttc,
                'currency' => $shipment->currency,
                'path' => '',
                'created_by' => Auth::id() ?? $shipment->created_by,
            ]);

            $path = "receipts/{$receipt->number}.pdf";
            Storage::disk('local')->put($path, $this->pdf->invoice($shipment, $receipt));

            // path is the only column allowed to settle after insert; the model blocks
            // ordinary updates, so this writes straight to the row.
            Receipt::withoutEvents(fn () => $receipt->newQuery()->whereKey($receipt->id)->update(['path' => $path]));

            return $receipt->refresh();
        });
    }

    /**
     * Sequential per year with no gaps: FA-2026-0001. Locks the year's rows so two agents
     * issuing at once cannot land on the same number.
     */
    private function nextNumber(): string
    {
        $year = now()->year;
        $prefix = "FA-{$year}-";

        $last = Receipt::query()
            ->where('number', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('number')
            ->value('number');

        $next = $last === null ? 1 : ((int) substr($last, strlen($prefix))) + 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
