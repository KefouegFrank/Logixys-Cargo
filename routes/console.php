<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Two weeks is long enough to notice a delivery problem and retry the jobs behind it.
Schedule::command('queue:prune-failed --hours=336')->daily();

// The IP and user agent on a contact message are only for near-term abuse triage
// (config('contact.triage_retention_days')); nothing else reads them once they're stale.
Schedule::command('contact-messages:prune')->daily();
