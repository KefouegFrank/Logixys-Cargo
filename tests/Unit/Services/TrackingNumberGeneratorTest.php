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

    public function test_matches_format_accepts_a_well_formed_number(): void
    {
        $this->assertTrue(TrackingNumberGenerator::matchesFormat('LGXY7K4M2QXAB'));
    }

    public function test_matches_format_rejects_wrong_prefix(): void
    {
        $this->assertFalse(TrackingNumberGenerator::matchesFormat('ABCD7K4M2QXAB'));
    }

    public function test_matches_format_rejects_ambiguous_characters(): void
    {
        // I, L, O, U are excluded from Crockford base32.
        $this->assertFalse(TrackingNumberGenerator::matchesFormat('LGXYILOUXXXXX'));
    }

    public function test_matches_format_rejects_wrong_length(): void
    {
        $this->assertFalse(TrackingNumberGenerator::matchesFormat('LGXY7K4M2QX'));
    }

    public function test_matches_format_rejects_garbage(): void
    {
        $this->assertFalse(TrackingNumberGenerator::matchesFormat('not a tracking number'));
    }
}
