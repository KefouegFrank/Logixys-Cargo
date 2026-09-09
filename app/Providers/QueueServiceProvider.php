<?php

namespace App\Providers;

use App\Mail\QueueJobFailed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Throwable;

class QueueServiceProvider extends ServiceProvider
{
    /** One alert per window, however many jobs fall over inside it. */
    private const ALERT_THROTTLE_MINUTES = 30;

    public function boot(): void
    {
        Queue::failing(function (JobFailed $event) {
            Log::critical('Queued job failed.', [
                'connection' => $event->connectionName,
                'job' => $event->job->resolveName(),
                'exception' => $event->exception->getMessage(),
            ]);

            $this->alert($event);
        });
    }

    private function alert(JobFailed $event): void
    {
        $recipient = config('brand.contact.email');

        if (blank($recipient) || ! Cache::add('queue-failure-alert', true, now()->addMinutes(self::ALERT_THROTTLE_MINUTES))) {
            return;
        }

        try {
            // Sent rather than queued: the queue is what just broke, and a queued alert
            // about a broken queue would sit in the same place nobody is watching.
            Mail::to($recipient)->send(new QueueJobFailed(
                $event->job->resolveName(),
                $event->exception->getMessage(),
            ));
        } catch (Throwable $exception) {
            Log::critical('Queue failure alert could not be sent.', ['exception' => $exception->getMessage()]);
        }
    }
}
