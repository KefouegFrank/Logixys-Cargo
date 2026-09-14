<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // User management is admin-only (doc section 11).
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->isNot($model);
    }

    // Bulk deletes are gated here, per record by delete() — without this method Filament
    // treats the bulk action as allowed and the self-delete guard above never runs.
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->isNot($model);
    }
}
