<?php

namespace Tests\Unit\Services;

use App\Services\TrackingNumberGenerator;
use PHPUnit\Framework\TestCase;

class TrackingNumberGeneratorTest extends TestCase
{
    public function test_normalize_uppercases_and_restores_the_canonical_shape(): void
    {
        $this->assertSame('LGXY013882535-CARGO', TrackingNumberGenerator::normalize(' lgxy-013882535-cargo '));
    }

    public function test_normalize_appends_the_suffix_when_it_is_missing(): void
    {
        $this->assertSame('LGXY013882535-CARGO', TrackingNumberGenerator::normalize('lgxy013882535'));
    }

    public function test_normalize_is_idempotent_on_an_already_clean_value(): void
    {
        $this->assertSame('LGXY013882535-CARGO', TrackingNumberGenerator::normalize('LGXY013882535-CARGO'));
    }

    public function test_matches_format_accepts_a_well_formed_number(): void
    {
        $this->assertTrue(TrackingNumberGenerator::matchesFormat('LGXY013882535-CARGO'));
    }

    public function test_matches_format_rejects_wrong_prefix(): void
    {
        $this->assertFalse(TrackingNumberGenerator::matchesFormat('CEEU013882535-CARGO'));
    }

    public function test_matches_format_rejects_a_missing_suffix(): void
    {
        $this->assertFalse(TrackingNumberGenerator::matchesFormat('LGXY013882535'));
    }

    public function test_matches_format_rejects_letters_in_the_serial(): void
    {
        $this->assertFalse(TrackingNumberGenerator::matchesFormat('LGXY01388253A-CARGO'));
    }

    public function test_matches_format_rejects_wrong_length(): void
    {
        $this->assertFalse(TrackingNumberGenerator::matchesFormat('LGXY01388-CARGO'));
    }

    public function test_matches_format_rejects_garbage(): void
    {
        $this->assertFalse(TrackingNumberGenerator::matchesFormat('not a tracking number'));
    }
}
