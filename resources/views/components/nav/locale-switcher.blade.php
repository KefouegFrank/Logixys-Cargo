@props(['variant' => 'bar'])

@php
    $locales = \App\Support\LocaleSwitcher::options();
    $current = \App\Support\LocaleSwitcher::current();
@endphp

@if ($variant === 'drawer')
    {{-- Own x-data scope: this "open" is local to the disclosure, distinct from
         the drawer's own "open" on <header>. The list floats over what's below
         it (position: absolute) rather than pushing the CTA/phone/email down. --}}
    <div
        x-data="{ open: false }"
        @click.outside="open = false"
        {{ $attributes->class('relative border-b border-line') }}
    >
        <button
            type="button"
            @click="open = ! open"
            :aria-expanded="open ? 'true' : 'false'"
            class="flex w-full items-center justify-between px-5 py-4 font-heading text-base font-bold text-ink"
        >
            <span class="flex items-center gap-2.5">
                <span class="text-lg leading-none" aria-hidden="true">{{ $current['flag'] }}</span>
                {{ $current['native'] }}
            </span>
            <x-heroicon-m-chevron-down class="h-4 w-4 shrink-0 transition-transform duration-300 ease-smooth" ::class="open && 'rotate-180'" aria-hidden="true" />
        </button>

        <ul
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-smooth duration-250"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="absolute inset-x-3 top-full z-20 mb-2 space-y-1 rounded-card bg-white p-2 shadow-raised ring-1 ring-line"
        >
            @foreach ($locales as $locale)
                <li>
                    <a
                        href="{{ $locale['url'] }}"
                        hreflang="{{ $locale['code'] }}"
                        @if ($locale['active']) aria-current="true" @endif
                        @class([
                            'flex items-center gap-2.5 rounded-field px-3 py-2 text-sm transition-colors duration-150',
                            'bg-surface-sunken font-bold text-ink' => $locale['active'],
                            'text-ink-muted hover:bg-surface-sunken hover:text-ink' => ! $locale['active'],
                        ])
                    >
                        <span class="text-base leading-none" aria-hidden="true">{{ $locale['flag'] }}</span>
                        <span>{{ $locale['native'] }}</span>
                    </a>
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
            <span class="text-base leading-none" aria-hidden="true">{{ $current['flag'] }}</span>
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
            class="absolute right-0 top-full z-50 mt-1 w-48 origin-top-right overflow-hidden rounded-card bg-white py-1 shadow-raised ring-1 ring-line"
        >
            <ul>
                @foreach ($locales as $locale)
                    <li>
                        <a
                            href="{{ $locale['url'] }}"
                            hreflang="{{ $locale['code'] }}"
                            @if ($locale['active']) aria-current="true" @endif
                            @class([
                                'flex items-center gap-2.5 px-4 py-2.5 text-sm transition-colors duration-150',
                                'bg-surface-sunken font-bold text-ink' => $locale['active'],
                                'text-ink-muted hover:bg-surface-sunken hover:text-ink' => ! $locale['active'],
                            ])
                        >
                            <span class="text-base leading-none" aria-hidden="true">{{ $locale['flag'] }}</span>
                            <span class="flex-1">{{ $locale['native'] }}</span>
                            <span class="font-heading text-xs font-bold text-ink-subtle">{{ $locale['short'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
