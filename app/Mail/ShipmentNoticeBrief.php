<?php

namespace App\Mail;

use App\Mail\Concerns\RetriesDelivery;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The office copy: what went out, to whom, and a link straight into the record. */
class ShipmentNoticeBrief extends Mailable
{
    use Queueable, RetriesDelivery, SerializesModels;

    /**
     * @param  array<int, string>  $notified
     * @param  array<int, string>  $skipped
     */
    public function __construct(
        public readonly Shipment $shipment,
        public readonly string $kind,
        public readonly array $notified,
        public readonly array $skipped = [],
        public readonly ?ShipmentEvent $event = null,
    ) {
        $this->locale(config('app.locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('notifications.shipment_brief.subject', [
                'tracking' => $this->shipment->tracking_number,
                'headline' => __('notifications.shipment_brief.kind.'.$this->kind),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.shipment-notice-brief', with: [
            'adminUrl' => route('filament.admin.resources.shipments.edit', $this->shipment),
        ]);
    }
}
