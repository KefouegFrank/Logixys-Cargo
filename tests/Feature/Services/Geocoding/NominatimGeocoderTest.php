<?php

namespace Tests\Feature\Services\Geocoding;

use App\Services\Geocoding\NominatimGeocoder;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NominatimGeocoderTest extends TestCase
{
    public function test_geocode_maps_lat_and_lon_from_the_response(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '47.5596432', 'lon' => '7.5885761', 'display_name' => 'Basel, Switzerland'],
            ]),
        ]);

        $result = (new NominatimGeocoder('LogixysCargo/1.0 (contact@logixyscargo.fr)'))->geocode('Bale, Suisse');

        $this->assertSame(47.5596432, $result['lat']);
        $this->assertSame(7.5885761, $result['lng']);
    }

    public function test_sends_the_configured_user_agent(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([])]);

        (new NominatimGeocoder('LogixysCargo/1.0 (contact@logixyscargo.fr)'))->geocode('Bale, Suisse');

        Http::assertSent(fn ($request) => $request->hasHeader('User-Agent', 'LogixysCargo/1.0 (contact@logixyscargo.fr)'));
    }

    public function test_returns_null_when_there_are_no_results(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([])]);

        $result = (new NominatimGeocoder('LogixysCargo/1.0'))->geocode('Entrepot 3, zone industrielle');

        $this->assertNull($result);
    }

    public function test_returns_null_on_a_failed_response(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response(null, 503)]);

        $result = (new NominatimGeocoder('LogixysCargo/1.0'))->geocode('Bale, Suisse');

        $this->assertNull($result);
    }
}
