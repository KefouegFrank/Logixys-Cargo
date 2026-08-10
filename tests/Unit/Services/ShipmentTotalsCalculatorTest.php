<?php

namespace Tests\Unit\Services;

use App\Services\ShipmentTotalsCalculator;
use PHPUnit\Framework\TestCase;

class ShipmentTotalsCalculatorTest extends TestCase
{
    public function test_sums_charges_and_applies_tax_rate(): void
    {
        $totals = (new ShipmentTotalsCalculator)->calculate(
            freightCost: 420,
            insuranceCost: 15,
            customsCost: 0,
            otherCost: 0,
            taxRate: 20,
        );

        $this->assertSame(435.0, $totals->totalHt);
        $this->assertSame(87.0, $totals->taxAmount);
        $this->assertSame(522.0, $totals->totalTtc);
    }

    public function test_zero_tax_rate_leaves_total_ttc_equal_to_total_ht(): void
    {
        $totals = (new ShipmentTotalsCalculator)->calculate(
            freightCost: 2100,
            insuranceCost: 90,
            customsCost: 150,
            otherCost: 0,
            taxRate: 0,
        );

        $this->assertSame(2340.0, $totals->totalHt);
        $this->assertSame(0.0, $totals->taxAmount);
        $this->assertSame(2340.0, $totals->totalTtc);
    }

    public function test_rounds_to_two_decimal_places(): void
    {
        $totals = (new ShipmentTotalsCalculator)->calculate(
            freightCost: 10.005,
            insuranceCost: 0,
            customsCost: 0,
            otherCost: 0,
            taxRate: 20,
        );

        $this->assertSame(10.01, $totals->totalHt);
        $this->assertSame(2.0, $totals->taxAmount);
        $this->assertSame(12.01, $totals->totalTtc);
    }
}
