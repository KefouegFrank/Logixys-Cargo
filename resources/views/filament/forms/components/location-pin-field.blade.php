@php
    $statePath = $field->getStatePath();
@endphp

<div
    wire:ignore
    x-data="{
        map: null,
        marker: null,
        state: $wire.{!! $field->applyStateBindingModifiers("\$entangle('{$statePath}')") !!},
        defaultCenter: [{{ $field->getCenterLat() }}, {{ $field->getCenterLng() }}],
        init() {
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            });

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

            this.map.on('click', (event) => {
                this.setPosition(event.latlng.lat, event.latlng.lng, true);
            });

            this.$watch('state', (value) => {
                if (value.lat && value.lng) {
                    this.placeMarker(value.lat, value.lng);
                    this.map.setView([value.lat, value.lng], Math.max(this.map.getZoom(), 13));
                } else if (this.marker) {
                    this.map.removeLayer(this.marker);
                    this.marker = null;
                }
            });

            // Maps initialised inside a still-animating modal can size themselves at 0x0.
            setTimeout(() => this.map.invalidateSize(), 300);
        },
        placeMarker(lat, lng) {
            if (this.marker) {
                this.marker.setLatLng([lat, lng]);
                return;
            }

            this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
            this.marker.on('dragend', () => {
                const position = this.marker.getLatLng();
                this.setPosition(position.lat, position.lng, true);
            });
        },
        setPosition(lat, lng, isManual) {
            this.state = { lat: lat, lng: lng, isManual: isManual };
        },
        clearPosition() {
            this.state = { lat: null, lng: null, isManual: false };
        },
    }"
    x-init="init()"
>
    <div x-ref="map" style="height: 320px; border-radius: 0.5rem;"></div>

    <p class="fi-fo-field-wrp-helper-text text-sm text-gray-500 dark:text-gray-400 mt-2" x-show="state.lat && state.lng">
        <span x-show="!state.isManual" x-cloak>Position from geocoding. Drag the pin or click the map to correct it.</span>
        <span x-show="state.isManual" x-cloak>
            Manually positioned.
            <button type="button" x-on:click="clearPosition()" class="underline">Clear and use automatic geocoding</button>
        </span>
    </p>
    <p class="fi-fo-field-wrp-helper-text text-sm text-gray-500 dark:text-gray-400 mt-2" x-show="!(state.lat && state.lng)" x-cloak>
        No position yet. It will be geocoded from the location above, or click the map to place a pin manually.
    </p>
</div>
