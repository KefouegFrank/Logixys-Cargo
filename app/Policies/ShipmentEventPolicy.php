<?php

namespace App\Policies;

use App\Models\ShipmentEvent;
use App\Models\User;

class ShipmentEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ShipmentEvent $shipmentEvent): bool
    {
        return true;
    }

    // Agents can add events (doc section 11).
    public function create(User $user): bool
    {
        return true;
    }

    // Append-only for every role; corrections are new events, not edits (doc section 6).
    public function update(User $user, ShipmentEvent $shipmentEvent): bool
    {
        return false;
    }

    public function delete(User $user, ShipmentEvent $shipmentEvent): bool
    {
        return false;
    }

    public function restore(User $user, ShipmentEvent $shipmentEvent): bool
    {
        return false;
    }

    public function forceDelete(User $user, ShipmentEvent $shipmentEvent): bool
    {
        return false;
    }
}
