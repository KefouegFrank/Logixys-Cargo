@php
    $statePath = $field->getStatePath();

    // Built here rather than inline: the nested quotes break Blade's parser in an attribute.
    $binding = '$wire.'.$field->applyStateBindingModifiers("\$entangle('{$statePath}')");
@endphp

<x-dynamic-component :component="$field->getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: {!! $binding !!},
            fills: @js($field->getFillPaths()),
            country: @js($field->getCountryHint()),
            endpoint: @js(route('admin.address-search')),
            minLength: @js(App\Services\AddressSearch\AddressSearchService::MIN_QUERY_LENGTH),
            results: [],
            open: false,
            loading: false,
            highlighted: -1,
            timer: null,
            controller: null,

            search() {
                clearTimeout(this.timer);

                if (! this.state || this.state.length < this.minLength) {
                    this.results = [];
                    this.open = false;
                    return;
                }

                // One request per pause in typing, not one per keystroke.
                this.timer = setTimeout(() => this.fetch(), 300);
            },

            async fetch() {
                // Abandon the in-flight lookup so a slow one can't overwrite a newer one.
                this.controller?.abort();
                this.controller = new AbortController();
                this.loading = true;

                try {
                    const url = new URL(this.endpoint, window.location.origin);
                    url.searchParams.set('q', this.state);
                    if (this.country) url.searchParams.set('country', this.country);

                    const response = await fetch(url, {
                        headers: { Accept: 'application/json' },
                        signal: this.controller.signal,
                    });

                    if (! response.ok) throw new Error(response.status);

                    this.results = (await response.json()).results ?? [];
                    this.highlighted = -1;
                    this.open = this.results.length > 0;
                } catch (e) {
                    if (e.name !== 'AbortError') {
                        this.results = [];
                        this.open = false;
                    }
                } finally {
                    this.loading = false;
                }
            },

            choose(result) {
                this.state = result.label;
                this.open = false;
                this.results = [];

                // Everything the agent would otherwise retype, filled from the one pick.
                Object.entries(this.fills).forEach(([key, path]) => {
                    if (result[key] !== null && result[key] !== undefined && result[key] !== '') {
                        $wire.set(path, result[key], false);
                    }
                });
            },

            move(step) {
                if (! this.open || this.results.length === 0) return;

                this.highlighted = (this.highlighted + step + this.results.length) % this.results.length;
            },
        }"
        x-on:keydown.escape="open = false"
        x-on:click.outside="open = false"
        style="position:relative"
    >
        {{-- Filament's own input, so the field is styled by the panel rather than by
             utility classes this view would have to keep in step with. --}}
        <x-filament::input.wrapper :disabled="$field->isDisabled()">
            <x-filament::input
                type="text"
                x-model="state"
                x-on:input="search()"
                x-on:keydown.arrow-down.prevent="move(1)"
                x-on:keydown.arrow-up.prevent="move(-1)"
                x-on:keydown.enter.prevent="highlighted >= 0 && choose(results[highlighted])"
                autocomplete="off"
                :disabled="$field->isDisabled()"
                :placeholder="$field->getPlaceholder()"
            />
        </x-filament::input.wrapper>

        <span x-show="loading" x-cloak class="fi-loading" style="position:absolute;inset-inline-end:0.75rem;top:0.65rem;font-size:0.75rem;color:#9ca3af">…</span>

        <ul
            x-show="open"
            x-cloak
            x-transition.opacity.duration.150ms
            style="position:absolute;z-index:30;margin-top:0.25rem;max-height:18rem;width:100%;overflow-y:auto;border-radius:0.5rem;background:#fff;padding-block:0.25rem;font-size:0.875rem;box-shadow:0 10px 30px rgba(16,41,70,.18);border:1px solid rgba(16,41,70,.12)"
        >
            <template x-for="(result, index) in results" :key="index">
                <li
                    x-on:click="choose(result)"
                    x-on:mouseenter="highlighted = index"
                    :style="highlighted === index ? 'background:#f0f6fd' : ''"
                    style="cursor:pointer;padding:0.5rem 0.75rem;transition:background-color .12s ease"
                >
                    <span style="display:block;color:#102946" x-text="result.label"></span>
                    <span style="display:block;font-size:0.75rem;color:#9ca3af" x-text="result.source"></span>
                </li>
            </template>
        </ul>
    </div>
</x-dynamic-component>
