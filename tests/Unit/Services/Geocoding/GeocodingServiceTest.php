<?php

namespace Tests\Unit\Services\Geocoding;

use App\Services\Geocoding\GeocodingService;
use PHPUnit\Framework\TestCase;

class GeocodingServiceTest extends TestCase
{
    public function test_normalize_lowercases_trims_and_collapses_whitespace(): void
    {
        $this->assertSame('bale, suisse', GeocodingService::normalize('  Bale,  Suisse  '));
    }
}
