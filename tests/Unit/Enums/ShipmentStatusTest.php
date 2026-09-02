<?php

namespace Tests\Unit\Enums;

use App\Enums\ShipmentStatus;
use PHPUnit\Framework\TestCase;

class ShipmentStatusTest extends TestCase
{
    public function test_step_grouping(): void
    {
        $this->assertSame(1, ShipmentStatus::Pending->step());
        $this->assertSame(1, ShipmentStatus::PickedUp->step());
        $this->assertSame(2, ShipmentStatus::InTransit->step());
        $this->assertSame(2, ShipmentStatus::AtCustoms->step());
        $this->assertSame(3, ShipmentStatus::OutForDelivery->step());
        $this->assertSame(4, ShipmentStatus::Delivered->step());
    }

    public function test_exception_statuses_have_no_step(): void
    {
        $this->assertNull(ShipmentStatus::OnHold->step());
        $this->assertNull(ShipmentStatus::Returned->step());
        $this->assertNull(ShipmentStatus::Cancelled->step());
    }

    public function test_is_exception(): void
    {
        $this->assertTrue(ShipmentStatus::OnHold->isException());
        $this->assertTrue(ShipmentStatus::Returned->isException());
        $this->assertTrue(ShipmentStatus::Cancelled->isException());

        foreach (ShipmentStatus::cases() as $status) {
            if ($status->step() !== null) {
                $this->assertFalse($status->isException());
            }
        }
    }

    public function test_exception_severity(): void
    {
        $this->assertSame('warning', ShipmentStatus::OnHold->exceptionSeverity());
        $this->assertSame('danger', ShipmentStatus::Returned->exceptionSeverity());
        $this->assertSame('danger', ShipmentStatus::Cancelled->exceptionSeverity());
        $this->assertNull(ShipmentStatus::InTransit->exceptionSeverity());
    }

    public function test_step_milestones_cover_all_four_steps_with_a_representative_status(): void
    {
        $milestones = ShipmentStatus::stepMilestones();

        $this->assertCount(4, $milestones);

        foreach ($milestones as $step => $status) {
            $this->assertSame($step, $status->step());
        }
    }
}
