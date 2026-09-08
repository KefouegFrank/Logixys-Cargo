<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The "we got it" reply, in whichever site language the visitor was using. */
class ContactMessageAcknowledged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly ContactMessage $contactMessage)
    {
        $this->locale($contactMessage->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('contact.mail.ack_subject'),
            replyTo: [new Address(config('brand.contact.email'), config('app.name'))],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact-acknowledged');
    }
}
