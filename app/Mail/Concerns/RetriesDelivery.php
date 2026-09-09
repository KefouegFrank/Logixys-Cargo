<?php

namespace App\Mail\Concerns;

/**
 * Retry schedule for queued mail. A rejected address fails the same way every time and
 * burns through these quickly; the delays are here for Resend 5xx and network blips.
 */
trait RetriesDelivery
{
    public int $tries = 5;

    /** @return array<int, int> */
    public function backoff(): array
    {
        return [60, 300, 900, 3600];
    }
}
