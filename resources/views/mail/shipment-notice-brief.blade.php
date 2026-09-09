<x-mail::message>
# {{ __('notifications.shipment_brief.kind.'.$kind) }}

**{{ $shipment->tracking_number }}** — {{ $shipment->status->label() }}
{{ $shipment->origin_label }} → {{ $shipment->destination_label }}
@if ($event?->location_label)
{{ $event->occurred_at->translatedFormat('d/m/Y H:i') }}, {{ $event->location_label }}
@endif

@if ($notified !== [])
{{ __('notifications.shipment_brief.notified') }} {{ implode(', ', $notified) }}
@else
{{ __('notifications.shipment_brief.nobody') }}
@endif
@if ($skipped !== [])

{{ __('notifications.shipment_brief.skipped') }} {{ implode(', ', $skipped) }}
@endif

<x-mail::button :url="$adminUrl">
{{ __('notifications.shipment_brief.open') }}
</x-mail::button>
</x-mail::message>
