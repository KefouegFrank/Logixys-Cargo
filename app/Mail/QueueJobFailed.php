<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Sent to the company inbox the first time a queued job dies in a given window. */
class QueueJobFailed extends Mailable
{
    public function __construct(
        public readonly string $jobName,
        public readonly string $reason,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '['.config('app.name').'] Échec de traitement en file d\'attente');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.queue-job-failed');
    }
}
