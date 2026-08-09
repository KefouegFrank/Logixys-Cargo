<?php

namespace App\Policies;

use App\Models\Receipt;
use App\Models\User;

class ReceiptPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Receipt $receipt): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    // Receipts are immutable once issued (doc section 6); enforced at the model layer too.
    public function update(User $user, Receipt $receipt): bool
    {
        return false;
    }

    // Doc section 11 reserves "void" for admins, but no void mechanism exists
    // in the section 6 schema yet (no voided_at/status column on receipts).
    // Deferring this rule until that column exists; delete stays off for everyone
    // in the meantime since receipts must never disappear once issued.
    public function delete(User $user, Receipt $receipt): bool
    {
        return false;
    }

    public function restore(User $user, Receipt $receipt): bool
    {
        return false;
    }

    public function forceDelete(User $user, Receipt $receipt): bool
    {
        return false;
    }
}
