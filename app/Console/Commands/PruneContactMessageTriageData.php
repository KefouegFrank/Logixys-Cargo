<?php

namespace App\Console\Commands;

use App\Models\ContactMessage;
use Illuminate\Console\Command;

class PruneContactMessageTriageData extends Command
{
    protected $signature = 'contact-messages:prune';

    protected $description = "Clear the IP address and user agent off contact messages once they're past triage age";

    public function handle(): int
    {
        $cutoff = now()->subDays((int) config('contact.triage_retention_days'));

        $cleared = ContactMessage::query()
            ->carryingTriageDataOlderThan($cutoff)
            ->update(['ip_address' => null, 'user_agent' => null]);

        $this->info("Cleared triage data from {$cleared} contact message(s) older than {$cutoff->toDateString()}.");

        return self::SUCCESS;
    }
}
