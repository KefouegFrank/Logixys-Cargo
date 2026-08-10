<?php

namespace Tests\Unit\Services;

use App\Services\TrackingNumberGenerator;
use PHPUnit\Framework\TestCase;

class TrackingNumberGeneratorTest extends TestCase
{
    public function test_normalize_uppercases_and_strips_spaces_and_hyphens(): void
    {
        $this->assertSame('LGXY7K4M2QX9', TrackingNumberGenerator::normalize(' lgxy-7k4m-2qx9 '));
    }

    public function test_normalize_is_idempotent_on_an_already_clean_value(): void
    {
        $this->assertSame('LGXY7K4M2QX9', TrackingNumberGenerator::normalize('LGXY7K4M2QX9'));
    }
}
