<?php

namespace Tests\Feature\Services\Geocoding;

use App\Models\GeocodeCache;
use App\Services\Geocoding\GeocoderProvider;
use App\Services\Geocoding\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeocodingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calls_the_provider_and_caches_the_result_on_a_miss(): void
    {
        $provider = new class implements GeocoderProvider
        {
            public int $calls = 0;

            public function geocode(string $query): ?array
            {
                $this->calls++;

                return ['lat' => 47.5596, 'lng' => 7.5886];
            }

            public function name(): string
            {
                return 'fake';
            }
        };

        $result = (new GeocodingService($provider))->geocode('Bale, Suisse');

        $this->assertSame(47.5596, $result['lat']);
        $this->assertSame(7.5886, $result['lng']);
        $this->assertSame(1, $provider->calls);
        $this->assertDatabaseHas('geocode_cache', [
            'query_normalized' => 'bale, suisse',
            'provider' => 'fake',
        ]);
    }

    public function test_does_not_call_the_provider_on_a_cache_hit(): void
    {
        GeocodeCache::create([
            'query_normalized' => 'bale, suisse',
            'lat' => 47.5596, 'lng' => 7.5886, 'provider' => 'fake',
        ]);

        $provider = new class implements GeocoderProvider
        {
            public int $calls = 0;

            public function geocode(string $query): ?array
            {
                $this->calls++;

                return ['lat' => 0, 'lng' => 0];
            }

            public function name(): string
            {
                return 'fake';
            }
        };

        $result = (new GeocodingService($provider))->geocode('  BALE,   Suisse ');

        $this->assertSame(47.5596, $result['lat']);
        $this->assertSame(0, $provider->calls);
    }

    public function test_returns_null_and_caches_nothing_when_the_provider_fails(): void
    {
        $provider = new class implements GeocoderProvider
        {
            public function geocode(string $query): ?array
            {
                return null;
            }

            public function name(): string
            {
                return 'fake';
            }
        };

        $result = (new GeocodingService($provider))->geocode('Entrepot 3, zone industrielle');

        $this->assertNull($result);
        $this->assertDatabaseCount('geocode_cache', 0);
    }
}
