<?php

namespace App\Mail;

use App\Mail\Concerns\RetriesDelivery;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Services\ShipmentNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** What a sender or receiver is told, in their own language, about their shipment. */
class ShipmentNotice extends Mailable
{
    use Queueable, RetriesDelivery, SerializesModels;

    public function __construct(
        public readonly Shipment $shipment,
        /** One of ShipmentNotifier::CREATED, STATUS or UPDATED. */
        public readonly string $kind,
        /** 'shipper' or 'receiver' — only changes how the mail addresses the reader. */
        public readonly string $party,
        public readonly ?ShipmentEvent $event = null,
    ) {
        $this->locale($shipment->locale ?: config('app.locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('notifications.shipment.subject', [
                'tracking' => $this->shipment->tracking_number,
            ]),
            // From has to stay on the verified sending domain; replies go to the office.
            replyTo: [new Address(config('brand.contact.email'), config('app.name'))],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.shipment-notice', with: [
            'headline' => $this->kind === ShipmentNotifier::STATUS
                ? $this->shipment->status->label()
                : __('notifications.shipment.headline.'.$this->kind),
            'line' => $this->kind === ShipmentNotifier::STATUS
                ? __('notifications.shipment.lines.'.$this->shipment->status->value)
                : __('notifications.shipment.line.'.$this->kind),
            'recipientName' => $this->party === 'shipper' ? $this->shipment->shipper_name : $this->shipment->receiver_name,
            'trackingUrl' => route('tracking.show', [
                'locale' => $this->shipment->locale ?: config('app.locale'),
                'number' => $this->shipment->tracking_number,
            ]),
        ]);
    }
}
