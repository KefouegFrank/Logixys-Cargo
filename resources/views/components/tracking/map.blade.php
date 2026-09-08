{{--
    Read-only Leaflet map: the origin and destination pins, the trail through every
    public event that carries coordinates, and a truck on the most recent one.
--}}
@props(['shipment', 'events'])

@php
    $point = fn ($lat, $lng, ?string $label) => ($lat === null || $lng === null)
        ? null
        : ['lat' => (float) $lat, 'lng' => (float) $lng, 'label' => $label];

    $trail = $events
        ->filter(fn ($event) => $event->location_lat !== null && $event->location_lng !== null)
        ->map(fn ($event) => [
            'lat' => (float) $event->location_lat,
            'lng' => (float) $event->location_lng,
            'label' => $event->location_label,
            'status' => $event->status->label(),
            'date' => $event->occurred_at->translatedFormat('d/m/Y H:i'),
        ])
        ->values()
        ->all();

    $payload = [
        'origin' => $point($shipment->origin_lat, $shipment->origin_lng, $shipment->origin_label),
        'destination' => $point($shipment->destination_lat, $shipment->destination_lng, $shipment->destination_label),
        'trail' => $trail,
        'labels' => [
            'origin' => __('tracking.result_origin'),
            'destination' => __('tracking.result_destination'),
            'here' => __('tracking.map_here'),
        ],
    ];
@endphp

@if ($payload['origin'] || $payload['destination'] || $trail !== [])
    <x-tracking.section :heading="__('tracking.map_heading')">
        {{-- Same box as the admin's pin field: 420px tall, 0.5rem corners. --}}
        <div
            id="tracking-map"
            class="h-[320px] w-full overflow-hidden rounded-field border border-line bg-navy-50 sm:h-[420px]"
            aria-label="{{ __('tracking.map_heading') }}"
        ></div>

        <p id="tracking-map-error" hidden class="mt-2 text-sm text-danger-fg">{{ __('tracking.map_unavailable') }}</p>

        <script type="application/json" id="tracking-map-data">@json($payload)</script>
    </x-tracking.section>

    @push('head')
        <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
        <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
    @endpush

    @push('scripts')
        <script>
            (function () {
                var el = document.getElementById('tracking-map');
                var data = JSON.parse(document.getElementById('tracking-map-data').textContent);

                // Self-hosted, but a stripped asset directory shouldn't leave a blank box.
                if (typeof L === 'undefined') {
                    el.hidden = true;
                    document.getElementById('tracking-map-error').hidden = false;
                    return;
                }

                var dot = function (background, border) {
                    return L.divIcon({
                        className: '',
                        iconSize: [18, 18],
                        iconAnchor: [9, 9],
                        popupAnchor: [0, -10],
                        html: '<span style="display:block;width:18px;height:18px;border-radius:9999px;background:' +
                            background + ';border:3px solid ' + border + ';box-shadow:0 1px 4px rgba(16,41,70,.4)"></span>',
                    });
                };

                var truck = L.divIcon({
                    className: '',
                    iconSize: [46, 46],
                    iconAnchor: [23, 46],
                    popupAnchor: [0, -46],
                    html: '<span style="display:flex;width:46px;height:46px;border-radius:9999px;background:#102946;' +
                        'border:3px solid #fff;box-shadow:0 2px 10px rgba(16,41,70,.45);align-items:center;' +
                        'justify-content:center;font-size:22px;line-height:1">\u{1F69A}</span>',
                });

                var escapeHtml = function (value) {
                    return String(value == null ? '' : value).replace(/[&<>"']/g, function (character) {
                        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character];
                    });
                };

                var popup = function (title, lines) {
                    return '<div style="min-width:150px"><div style="font-weight:700;font-size:15px;color:#102946;margin-bottom:2px">' +
                        escapeHtml(title) + '</div>' +
                        lines.filter(Boolean).map(function (line) {
                            return '<div style="font-size:13px;color:#2e496a">' + escapeHtml(line) + '</div>';
                        }).join('') + '</div>';
                };

                // Scroll-zoom off so the page still scrolls when the cursor crosses the map.
                var map = L.map(el, { scrollWheelZoom: false });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19,
                }).addTo(map);

                var bounds = [];

                if (data.origin) {
                    L.marker([data.origin.lat, data.origin.lng], { icon: dot('#2e496a', '#ffffff') })
                        .addTo(map)
                        .bindPopup(popup(data.labels.origin, [data.origin.label]));
                    bounds.push([data.origin.lat, data.origin.lng]);
                }

                if (data.destination) {
                    L.marker([data.destination.lat, data.destination.lng], { icon: dot('#f9d52a', '#102946') })
                        .addTo(map)
                        .bindPopup(popup(data.labels.destination, [data.destination.label]));
                    bounds.push([data.destination.lat, data.destination.lng]);
                }

                data.trail.forEach(function (stop) {
                    bounds.push([stop.lat, stop.lng]);
                });

                if (data.trail.length > 1) {
                    L.polyline(data.trail.map(function (stop) { return [stop.lat, stop.lng]; }), {
                        color: '#2e496a',
                        weight: 3,
                        opacity: 0.7,
                        dashArray: '6 8',
                    }).addTo(map);
                }

                data.trail.slice(0, -1).forEach(function (stop) {
                    L.marker([stop.lat, stop.lng], { icon: dot('#a8bdd6', '#ffffff') })
                        .addTo(map)
                        .bindPopup(popup(stop.status, [stop.label, stop.date]));
                });

                var current = data.trail[data.trail.length - 1];

                if (current) {
                    L.marker([current.lat, current.lng], { icon: truck })
                        .addTo(map)
                        .bindPopup(popup(data.labels.here, [current.label, current.status, current.date]))
                        .openPopup();
                }

                if (bounds.length > 1) {
                    map.fitBounds(bounds, { padding: [48, 48] });
                } else if (bounds.length === 1) {
                    map.setView(bounds[0], 13);
                } else {
                    // Same fallback framing as the admin field: France, zoomed out.
                    map.setView([46.6034, 2.2137], 5);
                }

                // A map built while its column is still being laid out sizes itself at 0x0.
                setTimeout(function () { map.invalidateSize(); }, 300);
            })();
        </script>
    @endpush
@endif
