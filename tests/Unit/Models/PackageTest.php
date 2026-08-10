<?php

namespace Tests\Unit\Models;

use App\Models\Package;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    public function test_volume_m3_from_dimensions(): void
    {
        $package = new Package(['length_cm' => 100, 'width_cm' => 100, 'height_cm' => 100, 'quantity' => 1]);

        $this->assertSame(1.0, $package->volumeM3());
    }

    public function test_volume_is_null_without_dimensions(): void
    {
        $package = new Package(['quantity' => 1]);

        $this->assertNull($package->volumeM3());
    }

    public function test_total_volume_multiplies_by_quantity(): void
    {
        $package = new Package(['length_cm' => 100, 'width_cm' => 100, 'height_cm' => 100, 'quantity' => 3]);

        $this->assertSame(3.0, $package->totalVolumeM3());
    }

    public function test_volumetric_weight_uses_the_given_divisor(): void
    {
        $package = new Package(['length_cm' => 100, 'width_cm' => 50, 'height_cm' => 40, 'quantity' => 1]);

        // (100*50*40)/5000 = 40kg
        $this->assertSame(40.0, $package->totalVolumetricWeightKg(5000));
    }

    public function test_volumetric_weight_is_null_without_a_divisor(): void
    {
        $package = new Package(['length_cm' => 100, 'width_cm' => 50, 'height_cm' => 40, 'quantity' => 1]);

        $this->assertNull($package->totalVolumetricWeightKg(null));
    }

    public function test_chargeable_weight_is_the_greater_of_actual_and_volumetric(): void
    {
        // Light but bulky: volumetric (40kg) beats actual (5kg).
        $bulky = new Package(['weight_kg' => 5, 'length_cm' => 100, 'width_cm' => 50, 'height_cm' => 40, 'quantity' => 1]);
        $this->assertSame(40.0, $bulky->chargeableWeightKg(5000));

        // Dense: actual (500kg) beats volumetric (40kg).
        $dense = new Package(['weight_kg' => 500, 'length_cm' => 100, 'width_cm' => 50, 'height_cm' => 40, 'quantity' => 1]);
        $this->assertSame(500.0, $dense->chargeableWeightKg(5000));
    }

    public function test_chargeable_weight_falls_back_to_actual_without_a_divisor(): void
    {
        $package = new Package(['weight_kg' => 12, 'length_cm' => 100, 'width_cm' => 50, 'height_cm' => 40, 'quantity' => 1]);

        $this->assertSame(12.0, $package->chargeableWeightKg(null));
    }
}
