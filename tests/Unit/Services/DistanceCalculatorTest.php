<?php

namespace Tests\Unit\Services;

use App\Services\DistanceCalculator;
use PHPUnit\Framework\TestCase;

class DistanceCalculatorTest extends TestCase
{
    public function test_same_point_is_zero_distance(): void
    {
        $this->assertSame(0, (new DistanceCalculator)->calculate(48.8566, 2.3522, 48.8566, 2.3522));
    }

    public function test_one_degree_of_longitude_at_the_equator_is_about_111km(): void
    {
        $km = (new DistanceCalculator)->calculate(0, 0, 0, 1);

        $this->assertEqualsWithDelta(111, $km, 1);
    }

    public function test_one_degree_of_latitude_is_about_111km(): void
    {
        $km = (new DistanceCalculator)->calculate(0, 0, 1, 0);

        $this->assertEqualsWithDelta(111, $km, 1);
    }
}
