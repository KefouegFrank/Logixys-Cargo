<x-mail::message>
# {{ $headline }}

{{ __('notifications.shipment.greeting', ['name' => $recipientName]) }}

{{ __('notifications.shipment.intro_'.$party, ['tracking' => $shipment->tracking_number]) }}

{{ $line }}

<x-mail::panel>
**{{ __('notifications.shipment.tracking') }}:** {{ $shipment->tracking_number }}
**{{ __('notifications.shipment.route') }}:** {{ $shipment->origin_label }} → {{ $shipment->destination_label }}
**{{ __('notifications.shipment.status') }}:** {{ $shipment->status->label() }}
@if ($event?->occurred_at)
**{{ __('notifications.shipment.date') }}:** {{ $event->occurred_at->translatedFormat('d/m/Y H:i') }}
@endif
@if ($event?->location_label)
**{{ __('notifications.shipment.location') }}:** {{ $event->location_label }}
@endif
@if ($shipment->expected_delivery_date)
**{{ __('notifications.shipment.eta') }}:** {{ $shipment->expected_delivery_date->translatedFormat('d/m/Y') }}
@endif
@if ($event?->remarks)

{{ $event->remarks }}
@endif
</x-mail::panel>

<x-mail::button :url="$trackingUrl">
{{ __('notifications.shipment.track') }}
</x-mail::button>

{{ __('notifications.shipment.signoff') }}
{{ config('app.name') }}

<small>{{ __('notifications.shipment.auto') }}</small>
</x-mail::message>
