{{--
    The pin the agent set on the shipment form: event_location is geocoded into
    event_position, the marker can be dragged from there, and ShipmentEventRecorder
    stores the result on the event. Same map component, read-only.
--}}
@props(['shipment', 'events'])

@php
    $position = $events
        ->filter(fn ($event) => $event->location_lat !== null && $event->location_lng !== null)
        ->last();
@endphp

@if ($position)
    <x-tracking.section :heading="__('tracking.map_heading')">
        <x-shipment-map
            :position="['lat' => (float) $position->location_lat, 'lng' => (float) $position->location_lng]"
            :location-label="$position->location_label"
            :status="$position->status->label()"
            height="clamp(320px, 55vh, 420px)"
        />
    </x-tracking.section>

    @push('head')
        <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
        <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
    @endpush
@endif
