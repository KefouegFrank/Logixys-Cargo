<?php

namespace App\Services;

class PackageTotalsCalculator
{
    public const DEFAULT_DIVISOR = 5000;

    /**
     * Totals the package rows exactly as they sit in the form, so the summary under the
     * table stays live while an agent types — before any row is persisted.
     *
     * @param  iterable<array<string, mixed>>  $rows
     */
    public function calculate(iterable $rows, ?int $divisor = null): PackageTotals
    {
        // Falls back rather than reading 0 from a missing key, which would silently
        // drop volumetric weight while volume still looked right.
        $divisor = $divisor ?: (int) config('shipping.volumetric_divisor', self::DEFAULT_DIVISOR);

        if ($divisor <= 0) {
            $divisor = self::DEFAULT_DIVISOR;
        }

        $count = 0;
        $quantity = 0;
        $actualWeight = 0.0;
        $volumetricWeight = 0.0;
        $volume = 0.0;
        $value = 0.0;

        foreach ($rows as $row) {
            // The repeater keeps an untouched blank row on screen; it is not a package.
            if (self::isBlank($row)) {
                continue;
            }

            $quantityForRow = max(1, (int) ($row['quantity'] ?? 1));
            $length = (float) ($row['length_cm'] ?? 0);
            $width = (float) ($row['width_cm'] ?? 0);
            $height = (float) ($row['height_cm'] ?? 0);
            $cubicCm = $length * $width * $height;

            $count++;
            $quantity += $quantityForRow;
            $actualWeight += (float) ($row['weight_kg'] ?? 0);
            $volume += ($cubicCm / 1_000_000) * $quantityForRow;
            $value += (float) ($row['unit_value'] ?? 0) * $quantityForRow;

            if ($divisor > 0) {
                $volumetricWeight += ($cubicCm / $divisor) * $quantityForRow;
            }
        }

        return new PackageTotals(
            $count,
            $quantity,
            round($actualWeight, 2),
            round($volumetricWeight, 2),
            round(max($actualWeight, $volumetricWeight), 2),
            round($volume, 3),
            round($value, 2),
        );
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function isBlank(array $row): bool
    {
        foreach (['package_type', 'description', 'weight_kg', 'length_cm', 'width_cm', 'height_cm', 'unit_value'] as $field) {
            if (filled($row[$field] ?? null)) {
                return false;
            }
        }

        return true;
    }
}
