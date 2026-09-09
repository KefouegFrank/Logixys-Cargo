<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Two weeks is long enough to notice a delivery problem and retry the jobs behind it.
Schedule::command('queue:prune-failed --hours=336')->daily();
