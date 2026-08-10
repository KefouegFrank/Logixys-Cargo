<?php

namespace App\Services;

class ShipmentTotalsCalculator
{
    public function calculate(
        float $freightCost,
        float $insuranceCost,
        float $customsCost,
        float $otherCost,
        float $taxRate,
    ): ShipmentTotals {
        $totalHt = round($freightCost + $insuranceCost + $customsCost + $otherCost, 2);
        $taxAmount = round($totalHt * $taxRate / 100, 2);

        return new ShipmentTotals($totalHt, $taxAmount, round($totalHt + $taxAmount, 2));
    }
}
