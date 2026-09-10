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
    {{-- Heading stays on the page grid; the map itself runs to the screen edges, so a
         phone gets the whole width rather than a letterbox inside the gutters. --}}
    <section aria-label="{{ __('tracking.map_heading') }}">
        <x-layout.container>
            <div class="mx-auto max-w-4xl">
                <h2 class="border-b border-line pb-2 font-heading text-base font-bold text-ink sm:text-lg">
                    {{ __('tracking.map_heading') }}
                </h2>
            </div>
        </x-layout.container>

        <div class="mt-5">
            <x-shipment-map
                :position="['lat' => (float) $position->location_lat, 'lng' => (float) $position->location_lng]"
                :location-label="$position->location_label"
                :status="$position->status->label()"
                height="clamp(340px, 60vh, 520px)"
                radius="0"
            />
        </div>
    </section>

    @push('head')
        <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
        <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
    @endpush
@endif
