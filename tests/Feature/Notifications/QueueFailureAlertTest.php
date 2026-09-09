<?php

namespace Tests\Feature\Notifications;

use App\Mail\QueueJobFailed;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class QueueFailureAlertTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        cache()->forget('queue-failure-alert');
    }

    public function test_a_failed_job_alerts_the_office(): void
    {
        $this->fail_a_job();

        Mail::assertSent(QueueJobFailed::class, fn ($mail) => $mail->hasTo(config('brand.contact.email')));
    }

    public function test_the_alert_is_sent_not_queued(): void
    {
        $this->fail_a_job();

        // Queueing an alert about a broken queue would park it behind the same problem.
        Mail::assertNothingQueued();
    }

    public function test_a_burst_of_failures_sends_one_alert(): void
    {
        $this->fail_a_job();
        $this->fail_a_job();
        $this->fail_a_job();

        Mail::assertSent(QueueJobFailed::class, 1);
    }

    private function fail_a_job(): void
    {
        $job = \Mockery::mock(Job::class);
        $job->shouldReceive('resolveName')->andReturn('App\Mail\ShipmentStatusUpdated');

        Event::dispatch(new JobFailed('database', $job, new RuntimeException('Resend refused the request.')));
    }
}
