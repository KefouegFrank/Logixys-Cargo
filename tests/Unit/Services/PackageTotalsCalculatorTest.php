<?php

namespace Tests\Unit\Services;

use App\Services\PackageTotalsCalculator;
use Tests\TestCase;

class PackageTotalsCalculatorTest extends TestCase
{
    private const ROW = [
        'quantity' => 1, 'package_type' => 'conteneur',
        'length_cm' => 590, 'width_cm' => 235, 'height_cm' => 239,
        'weight_kg' => 3100, 'unit_value' => 1200,
    ];

    public function test_it_totals_a_row(): void
    {
        $totals = (new PackageTotalsCalculator)->calculate([self::ROW]);

        $this->assertSame(1, $totals->count);
        $this->assertSame(3100.0, $totals->actualWeightKg);
        $this->assertSame(6627.47, $totals->volumetricWeightKg);
        $this->assertSame(6627.47, $totals->chargeableWeightKg);
        $this->assertSame(33.137, $totals->volumeCbm);
    }

    public function test_a_missing_divisor_config_does_not_zero_the_volumetric_weight(): void
    {
        config()->offsetUnset('shipping');

        $totals = (new PackageTotalsCalculator)->calculate([self::ROW]);

        $this->assertSame(6627.47, $totals->volumetricWeightKg);
        $this->assertSame(6627.47, $totals->chargeableWeightKg);
    }

    public function test_an_untouched_blank_row_is_not_counted(): void
    {
        $totals = (new PackageTotalsCalculator)->calculate([self::ROW, ['quantity' => 1]]);

        $this->assertSame(1, $totals->count);
        $this->assertSame(1, $totals->quantity);
    }
}
