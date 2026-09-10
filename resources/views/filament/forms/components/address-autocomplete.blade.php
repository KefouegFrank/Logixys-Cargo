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
        class="relative"
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

        <span x-show="loading" x-cloak class="absolute end-3 top-2.5 text-xs text-gray-400 dark:text-gray-500">…</span>

        {{-- Panel styling is spelled out rather than reusing .fi-dropdown-panel: that class
             is built for action menus and caps itself at max-width 14rem !important, which
             an address never fits inside. --}}
        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-x-0 top-full z-20 mt-1 max-h-72 w-full overflow-y-auto overflow-x-hidden rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
        >
            <ul class="divide-y divide-gray-100 dark:divide-white/5" role="listbox">
                <template x-for="(result, index) in results" :key="index">
                    <li>
                        <button
                            type="button"
                            role="option"
                            :aria-selected="highlighted === index"
                            x-on:click="choose(result)"
                            x-on:mouseenter="highlighted = index"
                            :class="highlighted === index && 'bg-gray-50 dark:bg-white/5'"
                            {{-- Wrapping, not nowrap: a full address would otherwise force
                                 the panel to scroll sideways. --}}
                            class="block w-full whitespace-normal break-words px-4 py-2.5 text-start text-sm leading-snug text-gray-950 transition-colors duration-75 hover:bg-gray-50 dark:text-white dark:hover:bg-white/5"
                            x-text="result.label"
                        ></button>
                    </li>
                </template>
            </ul>
        </div>

    </div>
</x-dynamic-component>
