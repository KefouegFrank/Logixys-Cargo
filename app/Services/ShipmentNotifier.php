<?php

namespace App\Services;

use App\Mail\ShipmentNotice;
use App\Mail\ShipmentNoticeBrief;
use App\Models\MailSuppression;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Mails both parties whenever a shipment is registered, changes status, or has its
 * details edited, and copies the office on each.
 *
 * Notices are collected and sent once the request is done: an agent editing fields and
 * posting a status in the same save is one thing happening, not two, and the customer
 * should get one email for it. Registered outranks a status change, which outranks an
 * ordinary edit.
 */
class ShipmentNotifier
{
    public const CREATED = 'created';

    public const STATUS = 'status';

    public const UPDATED = 'updated';

    private const PRECEDENCE = [self::UPDATED => 1, self::STATUS => 2, self::CREATED => 3];

    /**
     * Changes that never warrant an edit notice: status is covered by its own mail, and
     * the rest are derived from columns already in the list.
     */
    private const DERIVED = [
        'status', 'delivered_at', 'updated_at',
        'origin_lat', 'origin_lng', 'destination_lat', 'destination_lng', 'distance_km',
    ];

    /** @var array<int, array{shipment: Shipment, kind: string, event: ShipmentEvent|null}> */
    private array $pending = [];

    private bool $flushScheduled = false;

    public function shipmentCreated(Shipment $shipment): void
    {
        $this->remember($shipment, self::CREATED);
    }

    public function statusChanged(Shipment $shipment, ShipmentEvent $event): void
    {
        $this->remember($shipment, self::STATUS, $event);
    }

    public function shipmentUpdated(Shipment $shipment): void
    {
        if (array_diff(array_keys($shipment->getChanges()), self::DERIVED) === []) {
            return;
        }

        $this->remember($shipment, self::UPDATED);
    }

    private function remember(Shipment $shipment, string $kind, ?ShipmentEvent $event = null): void
    {
        $existing = $this->pending[$shipment->id] ?? null;

        if ($existing !== null && self::PRECEDENCE[$existing['kind']] > self::PRECEDENCE[$kind]) {
            // A stronger notice already stands, but keep the event so it can carry the detail.
            $this->pending[$shipment->id]['event'] ??= $event;

            return;
        }

        $this->pending[$shipment->id] = [
            'shipment' => $shipment,
            'kind' => $kind,
            'event' => $event ?? $existing['event'] ?? null,
        ];

        $this->scheduleFlush();
    }

    private function scheduleFlush(): void
    {
        if ($this->flushScheduled) {
            return;
        }

        $this->flushScheduled = true;

        app()->terminating(fn () => $this->flush());
    }

    /** Sends everything collected so far. Safe to call twice; the queue is emptied first. */
    public function flush(): void
    {
        $pending = $this->pending;
        $this->pending = [];

        foreach ($pending as $notice) {
            $this->send($notice['shipment'], $notice['kind'], $notice['event']);
        }
    }

    private function send(Shipment $shipment, string $kind, ?ShipmentEvent $event): void
    {
        $notified = [];
        $skipped = [];

        foreach (['shipper', 'receiver'] as $party) {
            $address = $shipment->{"{$party}_email"};

            if (blank($address)) {
                continue;
            }

            // A bounced address keeps bouncing, and Resend counts that against the domain.
            if (MailSuppression::suppresses($address)) {
                $skipped[] = $address;

                continue;
            }

            $queued = $this->queue(
                fn () => Mail::to($address, $shipment->{"{$party}_name"})
                    ->queue(new ShipmentNotice($shipment, $kind, $party, $event)),
                $shipment,
            );

            $queued ? $notified[] = $address : $skipped[] = $address;
        }

        // The office copy goes out whatever became of the customer addresses — a shipment
        // nobody could be told about is the case worth hearing about.
        $office = config('brand.contact.email');

        if (filled($office)) {
            $this->queue(
                fn () => Mail::to($office)->queue(new ShipmentNoticeBrief($shipment, $kind, $notified, $skipped, $event)),
                $shipment,
            );
        }

        if ($notified !== [] && $event !== null) {
            $event->stampNotified();
        }
    }

    /** The shipment is already saved, so a queue that refuses the job is logged, not thrown. */
    private function queue(callable $dispatch, Shipment $shipment): bool
    {
        try {
            $dispatch();

            return true;
        } catch (Throwable $exception) {
            Log::error('Shipment notice could not be queued.', [
                'shipment_id' => $shipment->id,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
