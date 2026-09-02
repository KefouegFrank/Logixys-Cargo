{{-- Reads `open` from the x-data on <header>. --}}
<div
    x-cloak
    x-show="open"
    class="fixed inset-0 z-50 lg:hidden"
    role="dialog"
    aria-modal="true"
    id="mobile-menu"
>
    <div
        x-show="open"
        x-transition.opacity.duration.200ms
        @click="open = false"
        class="absolute inset-0 bg-navy-950/60"
        aria-hidden="true"
    ></div>

    <div
        x-show="open"
        x-trap.noscroll="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute inset-y-0 right-0 flex w-[85%] max-w-sm flex-col overflow-y-auto bg-white shadow-raised"
    >
        <div class="flex items-center justify-between border-b border-line px-5 py-4">
            <x-brand.logo class="h-9" />
            <button
                type="button"
                @click="open = false"
                class="-mr-1 rounded-field p-1.5 text-ink transition-[background-color,transform] duration-200 hover:rotate-90 hover:bg-surface-sunken focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus"
            >
                <span class="sr-only">{{ __('nav.close_menu') }}</span>
                <x-heroicon-o-x-mark class="h-6 w-6" aria-hidden="true" />
            </button>
        </div>

        <nav aria-label="{{ __('nav.main_nav') }}">
            @foreach (config('navigation.main') as $item)
                <x-nav.item :route="$item['route']" :label="$item['label']" variant="drawer" />
            @endforeach
        </nav>

        <x-nav.locale-switcher variant="drawer" />

        <div class="space-y-4 px-5 py-5">
            <x-ui.button
                variant="accent"
                size="lg"
                :href="route('tracking.index', ['locale' => app()->getLocale()])"
                class="w-full"
            >
                {{ __('nav.track_goods') }}
                <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
            </x-ui.button>

            <a href="tel:{{ config('brand.contact.phone_href') }}" class="group flex items-center gap-3 text-ink transition-colors duration-200 hover:text-accent-text">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-pill bg-accent transition-transform duration-300 ease-smooth group-hover:scale-110">
                    <x-heroicon-s-phone class="h-5 w-5 text-ink" aria-hidden="true" />
                </span>
                <span class="font-heading text-sm font-bold">{{ config('brand.contact.phone') }}</span>
            </a>

            <a href="mailto:{{ config('brand.contact.email') }}" class="group flex items-center gap-3 text-ink-muted transition-colors duration-200 hover:text-ink">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-pill bg-surface-sunken transition-colors duration-200 group-hover:bg-accent">
                    <x-heroicon-s-envelope class="h-5 w-5 text-ink" aria-hidden="true" />
                </span>
                <span class="break-all text-sm">{{ config('brand.contact.email') }}</span>
            </a>
        </div>
    </div>
</div>
