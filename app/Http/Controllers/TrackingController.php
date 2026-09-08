<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\TrackingNumberGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request, string $locale): View|RedirectResponse
    {
        $number = $request->query('number');

        if (filled($number)) {
            return redirect()->route('tracking.show', [
                'locale' => $locale,
                'number' => TrackingNumberGenerator::normalize($number),
            ]);
        }

        return view('tracking.index');
    }

    public function show(Request $request, string $locale, string $number): View
    {
        $normalized = TrackingNumberGenerator::normalize($number);

        if (! TrackingNumberGenerator::matchesFormat($normalized)) {
            return $this->notFound();
        }

        // The tracking number is the credential: it is mailed to the customer, validated
        // against the format before any query, and the lookup is rate limited. A holder of
        // a valid number sees the whole record, as on the carrier screens this replaces.
        $shipment = Shipment::with(['carrier', 'packages', 'events'])
            ->where('tracking_number', $normalized)
            ->first();

        if ($shipment === null) {
            return $this->notFound();
        }

        return view('tracking.show', ['shipment' => $shipment]);
    }

    private function notFound(): View
    {
        return view('tracking.not-found');
    }
}
