@php
    $statePath = $field->getStatePath();
    $locationPath = $field->getLocationStatePath();
    $statusPath = $field->getStatusStatePath();
@endphp

<div
    wire:ignore
    x-data="{
        map: null,
        marker: null,
        failed: false,
        state: $wire.{!! $field->applyStateBindingModifiers("\$entangle('{$statePath}')") !!},
        @if ($locationPath)
            locationLabel: $wire.{!! $field->applyStateBindingModifiers("\$entangle('{$locationPath}')") !!},
        @else
            locationLabel: null,
        @endif
        @if ($statusPath)
            statusValue: $wire.{!! $field->applyStateBindingModifiers("\$entangle('{$statusPath}')") !!},
        @else
            statusValue: null,
        @endif
        statusLabels: @js($field->getStatusLabels()),
        defaultCenter: [{{ $field->getCenterLat() }}, {{ $field->getCenterLng() }}],

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

            this.map.on('click', (event) => {
                this.setPosition(event.latlng.lat, event.latlng.lng, true);
            });

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

        vehicleIcon() {
            return L.divIcon({
                className: '',
                iconSize: [46, 46],
                iconAnchor: [23, 46],
                popupAnchor: [0, -46],
                // Single quotes only: this string lives inside the x-data attribute, and a
                // double quote would close it.
                html: `<div style='width:46px;height:46px;border-radius:9999px;background:#102946;border:3px solid #fff;box-shadow:0 2px 10px rgba(16,41,70,.45);display:flex;align-items:center;justify-content:center;font-size:22px;line-height:1'>🚚</div>`,
            });
        },

        popupHtml() {
            const status = this.statusLabels[this.statusValue] ?? null;
            const rows = [this.locationLabel, status].filter(Boolean).map(
                (line) => `<div style='font-size:13px;color:#2e496a'>${line}</div>`
            ).join('');

            return `<div style='min-width:150px'><div style='font-weight:700;font-size:15px;color:#102946;margin-bottom:2px'>Votre colis est ici</div>${rows}</div>`;
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

            this.marker = L.marker([lat, lng], { draggable: true, icon: this.vehicleIcon() })
                .addTo(this.map)
                .bindPopup(this.popupHtml(), { closeButton: true, offset: [0, 0] })
                .openPopup();

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
    <div x-ref="map" style="height: 420px; border-radius: 0.5rem;"></div>

    <p class="fi-fo-field-wrp-helper-text text-sm mt-2" style="color:#b91c1c" x-show="failed" x-cloak>
        La bibliothèque de cartographie n'a pas pu être chargée.
    </p>
</div>
