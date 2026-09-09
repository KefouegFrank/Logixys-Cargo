<?php

namespace App\Services;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipmentEventRecorder
{
    /** The sidebar's own fields, which are not shipment columns. */
    public const FIELDS = ['event_date', 'event_time', 'event_location', 'event_status', 'event_remarks', 'event_is_public', 'event_position'];

    /**
     * Splits the sidebar fields out of submitted form data.
     *
     * @param  array<string, mixed>  $data
     * @return array{0: array<string, mixed>, 1: array<string, mixed>} event data, then the rest
     */
    public static function split(array $data): array
    {
        $event = [];

        foreach (self::FIELDS as $field) {
            if (array_key_exists($field, $data)) {
                $event[$field] = $data[$field];
                unset($data[$field]);
            }
        }

        return [$event, $data];
    }

    /**
     * Appends a tracking event and moves the shipment onto that status. Does nothing when
     * no status was picked, so an ordinary save never adds a row to the history.
     *
     * @param  array<string, mixed>  $event
     */
    public function record(Shipment $shipment, array $event): bool
    {
        $status = $event['event_status'] ?? null;

        if (blank($status)) {
            return false;
        }

        $status = $status instanceof ShipmentStatus ? $status : ShipmentStatus::from($status);

        $record = DB::transaction(function () use ($shipment, $event, $status) {
            $record = $shipment->events()->create([
                'status' => $status,
                'location_label' => $event['event_location'] ?? null,
                'location_lat' => $event['event_position']['lat'] ?? null,
                'location_lng' => $event['event_position']['lng'] ?? null,
                'is_manual_position' => (bool) ($event['event_position']['isManual'] ?? false),
                'occurred_at' => self::occurredAt($event),
                'remarks' => $event['event_remarks'] ?? null,
                'is_public' => (bool) ($event['event_is_public'] ?? true),
                'created_by' => Auth::id() ?? $shipment->created_by,
            ]);

            $shipment->status = $status;

            if ($status === ShipmentStatus::Delivered && $shipment->delivered_at === null) {
                $shipment->delivered_at = now();
            }

            $shipment->save();

            return $record;
        });

        // Outside the transaction: a rollback must not leave mail already on the queue.
        app(ShipmentNotifier::class)->statusChanged($shipment->refresh(), $record);

        return true;
    }

    /** @param array<string, mixed> $event */
    private static function occurredAt(array $event): Carbon
    {
        $date = $event['event_date'] ?? null;
        $time = $event['event_time'] ?? null;

        if (blank($date)) {
            return now();
        }

        return Carbon::parse(Carbon::parse($date)->toDateString().' '.(blank($time) ? '00:00' : $time));
    }
}
