@props(['variant' => 'bar'])

@php
    $locales = \App\Support\LocaleSwitcher::options();
    $current = \App\Support\LocaleSwitcher::current();
@endphp

@if ($variant === 'drawer')
    {{-- No dropdown in the drawer: the panel is already a menu, so the codes sit inline. --}}
    <div {{ $attributes->class('px-5 py-4') }}>
        <p class="mb-2.5 font-heading text-xs font-bold uppercase tracking-widest text-ink-muted">
            {{ __('nav.language') }}
        </p>
        <ul class="flex flex-wrap gap-2">
            @foreach ($locales as $locale)
                <li>
                    <a
                        href="{{ $locale['url'] }}"
                        hreflang="{{ $locale['code'] }}"
                        @if ($locale['active']) aria-current="true" @endif
                        @class([
                            'block rounded-field border px-3 py-1.5 font-heading text-xs font-bold transition-colors duration-200',
                            'border-ink bg-ink text-white' => $locale['active'],
                            'border-line text-ink hover:border-navy-900 hover:bg-navy-900 hover:text-white' => ! $locale['active'],
                        ])
                    >{{ $locale['short'] }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div
        x-data="{ open: false }"
        @keydown.escape.window="open = false"
        @click.outside="open = false"
        {{ $attributes->class('relative') }}
    >
        <button
            type="button"
            @click="open = ! open"
            :aria-expanded="open ? 'true' : 'false'"
            aria-haspopup="true"
            class="flex items-center gap-1.5 rounded-field px-2.5 py-2 font-heading text-sm font-bold text-ink transition-colors duration-200 hover:bg-ink/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus"
        >
            <span class="sr-only">{{ __('nav.choose_language') }}</span>
            <x-heroicon-o-globe-alt class="h-5 w-5" aria-hidden="true" />
            <span aria-hidden="true">{{ $current['short'] }}</span>
            <x-heroicon-m-chevron-down class="h-4 w-4 transition-transform duration-300 ease-smooth" ::class="open && 'rotate-180'" aria-hidden="true" />
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-smooth duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
            class="absolute right-0 top-full z-50 mt-1 w-44 origin-top-right overflow-hidden rounded-card bg-white py-1 shadow-raised ring-1 ring-line"
        >
            <ul>
                @foreach ($locales as $locale)
                    <li>
                        <a
                            href="{{ $locale['url'] }}"
                            hreflang="{{ $locale['code'] }}"
                            @if ($locale['active']) aria-current="true" @endif
                            @class([
                                'flex items-center justify-between gap-3 px-4 py-2.5 text-sm transition-colors duration-150',
                                'bg-surface-sunken font-bold text-ink' => $locale['active'],
                                'text-ink-muted hover:bg-surface-sunken hover:text-ink' => ! $locale['active'],
                            ])
                        >
                            <span>{{ $locale['native'] }}</span>
                            <span class="font-heading text-xs font-bold text-ink-subtle">{{ $locale['short'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
