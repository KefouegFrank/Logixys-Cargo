{{--
    `open` is shared by the hamburger below and the drawer it triggers.
    `scrolled` drives the sticky-header shrink: past the threshold, the top
    bar collapses (see top-bar.blade.php) and the header gains a shadow —
    the nav row and logo are unaffected, so what's left just looks compact.
--}}
<header
    x-data="{ open: false, scrolled: window.scrollY > 24 }"
    @keydown.escape.window="open = false"
    @scroll.window.throttle.150ms.passive="scrolled = window.scrollY > 24"
    :class="scrolled && 'shadow-raised'"
    class="sticky top-0 z-40 transition-shadow duration-300"
>
    {{--
        Desktop: a 2-column grid, not two stacked bands. The logo spans both
        rows (row-span-2), so its height is whatever the two rows add up to —
        no hardcoded height to keep in sync. Both rows start in column 2, so
        the top bar's address and the nav's first link share one left edge.
    --}}
    <div class="hidden lg:grid lg:grid-cols-[auto_1fr]">
        <a
            href="{{ route('home', ['locale' => app()->getLocale()]) }}"
            class="row-span-2 flex w-72 items-center justify-center bg-navy-900 px-8 xl:w-80"
        >
            <x-brand.logo variant="light" class="h-11" />
        </a>

        <x-layout.top-bar />
        <x-layout.nav-bar />
    </div>

    {{-- Mobile: one row, logo and hamburger only — no top bar. --}}
    <div class="flex items-stretch bg-navy-900 lg:hidden">
        <a
            href="{{ route('home', ['locale' => app()->getLocale()]) }}"
            class="flex items-center px-4 py-5 sm:px-6"
        >
            <x-brand.logo variant="light" class="h-8 sm:h-9" />
        </a>

        <button
            type="button"
            @click="open = true"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="mobile-menu"
            class="ml-auto flex shrink-0 items-center px-4 text-accent transition-colors duration-200 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent sm:px-6"
        >
            <span class="sr-only">{{ __('nav.open_menu') }}</span>
            <x-heroicon-o-bars-3 class="h-8 w-8" aria-hidden="true" />
        </button>
    </div>

    <x-layout.mobile-menu />
</header>
