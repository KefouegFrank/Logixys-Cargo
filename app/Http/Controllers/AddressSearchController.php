<?php

namespace App\Http\Controllers;

use App\Services\AddressSearch\AddressSearchService;
use App\Services\AddressSearch\AddressSuggestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Suggestions for the booking form. Server-side so the Geoapify and LocationIQ keys
 * never reach a browser, and behind the panel's own auth so it isn't a free proxy.
 */
class AddressSearchController extends Controller
{
    public function __invoke(Request $request, AddressSearchService $addresses): JsonResponse
    {
        // Checked here rather than by the auth middleware: this is fetched as JSON, and
        // that middleware would answer a signed-out agent with a redirect to a login
        // route the Filament panel does not publish under that name.
        abort_if($request->user() === null, 401);
        abort_unless($request->user()->is_active, 403);

        $validated = $request->validate([
            'q' => ['required', 'string', 'max:200'],
            'country' => ['nullable', 'string', 'size:2', 'alpha'],
        ]);

        $suggestions = $addresses->search($validated['q'], $validated['country'] ?? null);

        return response()->json([
            'results' => array_map(fn (AddressSuggestion $s) => $s->toArray(), $suggestions),
        ]);
    }
}
