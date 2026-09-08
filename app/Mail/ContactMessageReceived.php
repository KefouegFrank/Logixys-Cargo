<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The submission, sent to the company inbox. */
class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('contact.mail.internal_subject', [
                'subject' => $this->contactMessage->subject->label(),
                'name' => $this->contactMessage->name,
            ]),
            // From stays the verified sending domain — Resend rejects anything else.
            // The visitor's address goes on Reply-To so a reply reaches them directly.
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact-received');
    }
}
