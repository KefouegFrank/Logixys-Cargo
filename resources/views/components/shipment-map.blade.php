{{--
    The one Leaflet map in the app. The admin's pin field passes Livewire expressions and
    gets a draggable marker; the public tracking page passes a fixed position and gets the
    same map read-only. Styling is inline because the Filament panel has no Tailwind build.
--}}
@props([
    'editable' => false,
    // Raw JS, only when editable: Livewire entangle expressions from LocationPinField.
    'stateExpression' => null,
    'locationExpression' => null,
    'statusExpression' => null,
    // Plain values, when not editable.
    'position' => null,
    'locationLabel' => null,
    'status' => null,
    'statusLabels' => [],
    'popupTitle' => null,
    'centerLat' => 46.6034,
    'centerLng' => 2.2137,
    'height' => '420px',
    // 'vehicle' is the shipment pin; 'office' is the static one on the contact page.
    'variant' => 'vehicle',
])

@php
    use Illuminate\Support\Js;

    $stateJs = $editable
        ? $stateExpression
        : Js::from([
            'lat' => $position['lat'] ?? null,
            'lng' => $position['lng'] ?? null,
            'isManual' => false,
        ])->toHtml();

    $locationJs = $editable ? $locationExpression : Js::from($locationLabel)->toHtml();
    $statusJs = $editable ? $statusExpression : Js::from($status)->toHtml();
@endphp

<div
    wire:ignore
    x-data="{
        map: null,
        marker: null,
        failed: false,
        editable: @js((bool) $editable),
        variant: @js($variant),
        state: {!! $stateJs !!},
        locationLabel: {!! $locationJs !!},
        statusValue: {!! $statusJs !!},
        statusLabels: @js((object) $statusLabels),
        popupTitle: @js($popupTitle ?? __('tracking.map_here')),
        defaultCenter: [{{ $centerLat }}, {{ $centerLng }}],

        init() {
            if (typeof L === 'undefined') {
                this.failed = true;
                return;
            }

            // A re-render can hand back the same node with its panes stripped; Leaflet
            // still sees its own id on it and refuses to rebuild.
            if (this.$refs.map._leaflet_id !== undefined) {
                this.map?.remove();
                this.map = null;
                delete this.$refs.map._leaflet_id;
                this.$refs.map.innerHTML = '';
            }

            // On edit, a field with no matching model attribute can hydrate as a bare
            // null instead of the {lat,lng,isManual} shape — normalize it back so every
            // later read of `state` can assume that shape.
            if (this.state === null || this.state === undefined) {
                this.state = { lat: null, lng: null, isManual: false };
            }

            const hasPosition = this.state.lat && this.state.lng;

            this.map = L.map(this.$refs.map).setView(
                hasPosition ? [this.state.lat, this.state.lng] : this.defaultCenter,
                hasPosition ? 13 : 5,
            );

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(this.map);

            if (hasPosition) {
                this.placeMarker(this.state.lat, this.state.lng);
            }

            if (this.editable) {
                this.map.on('click', (event) => {
                    this.setPosition(event.latlng.lat, event.latlng.lng, true);
                });
            }

            this.$watch('state', (value) => {
                if (value?.lat && value?.lng) {
                    this.placeMarker(value.lat, value.lng);
                    this.map.setView([value.lat, value.lng], Math.max(this.map.getZoom(), 13));
                } else if (this.marker) {
                    this.map.removeLayer(this.marker);
                    this.marker = null;
                }
            });

            this.$watch('locationLabel', () => this.refreshPopup());
            this.$watch('statusValue', () => this.refreshPopup());

            // A map built while its column is still being laid out sizes itself at 0x0.
            setTimeout(() => this.map.invalidateSize(), 300);
        },

        markerIcon() {
            // Single quotes only: this string lives inside the x-data attribute, and a
            // double quote would close it.
            const glyph = this.variant === 'office' ? '📍' : '🚚';

            return L.divIcon({
                className: '',
                iconSize: [46, 46],
                iconAnchor: [23, 46],
                popupAnchor: [0, -46],
                html: `<div style='width:46px;height:46px;border-radius:9999px;background:#102946;border:3px solid #fff;box-shadow:0 2px 10px rgba(16,41,70,.45);display:flex;align-items:center;justify-content:center;font-size:22px;line-height:1'>${glyph}</div>`,
            });
        },

        popupHtml() {
            const status = this.statusLabels[this.statusValue] ?? this.statusValue ?? null;
            const rows = [this.locationLabel, status].filter(Boolean).map(
                (line) => `<div style='font-size:13px;color:#2e496a'>${line}</div>`
            ).join('');

            return `<div style='min-width:150px'><div style='font-weight:700;font-size:15px;color:#102946;margin-bottom:2px'>${this.popupTitle}</div>${rows}</div>`;
        },

        refreshPopup() {
            this.marker?.setPopupContent(this.popupHtml());
        },

        placeMarker(lat, lng) {
            if (this.marker) {
                this.marker.setLatLng([lat, lng]);
                this.refreshPopup();
                return;
            }

            this.marker = L.marker([lat, lng], { draggable: this.editable, icon: this.markerIcon() })
                .addTo(this.map)
                .bindPopup(this.popupHtml(), { closeButton: true, offset: [0, 0] })
                .openPopup();

            if (! this.editable) {
                return;
            }

            this.marker.on('dragend', () => {
                const position = this.marker.getLatLng();
                this.setPosition(position.lat, position.lng, true);
            });
        },

        setPosition(lat, lng, isManual) {
            this.state = { lat: lat, lng: lng, isManual: isManual };
        },
    }"
>
    <div x-ref="map" style="height: {{ $height }}; border-radius: 0.5rem;"></div>

    <p class="mt-2 text-sm" style="color:#b91c1c" x-show="failed" x-cloak>{{ __('tracking.map_unavailable') }}</p>
</div>
