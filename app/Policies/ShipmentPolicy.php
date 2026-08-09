<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Shipment $shipment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Shipment $shipment): bool
    {
        return true;
    }

    // Agents cannot delete shipments (doc section 11).
    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }
}
