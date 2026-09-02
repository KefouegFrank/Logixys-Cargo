{{--
    Navy below lg (matching the mobile design), gold from lg up. The band is
    full-bleed; the container inside keeps the menu aligned with page content.
    The logo keeps its own navy block at every size, so one light lockup works.
--}}
{{-- overflow-x-clip (not hidden) contains the logo bleed without making this a
     scroll container, so the language dropdown can still overflow downward. --}}
<div class="bg-navy-900 lg:overflow-x-clip lg:bg-accent">
    <x-layout.container :padded="false" class="flex items-stretch">
        <a
            href="{{ route('home', ['locale' => app()->getLocale()]) }}"
            class="relative flex shrink-0 items-center bg-navy-900 px-4 py-3.5 before:absolute before:inset-y-0 before:right-full before:w-screen before:bg-navy-900 sm:px-6 lg:px-8 lg:py-4"
        >
            <x-brand.logo variant="light" class="h-8 sm:h-9 lg:h-10" />
        </a>

        {{-- German is the widest locale and needs ~890px for logo + menu + actions;
             the gaps tighten at lg so 1024 keeps real headroom. --}}
        <nav class="hidden lg:flex lg:flex-1 lg:items-center lg:gap-5 lg:px-5 xl:gap-9 xl:px-8" aria-label="{{ __('nav.main_nav') }}">
            @foreach (config('navigation.main') as $item)
                <x-nav.item :route="$item['route']" :label="$item['label']" />
            @endforeach
        </nav>

        <div class="ml-auto hidden shrink-0 items-center gap-1 pr-4 sm:pr-6 lg:flex lg:pr-8 xl:gap-3">
            <x-nav.locale-switcher />

            <x-ui.button
                variant="light"
                :href="route('tracking.index', ['locale' => app()->getLocale()])"
                class="whitespace-nowrap"
            >
                {{ __('nav.track_goods') }}
                <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
            </x-ui.button>
        </div>

        <button
            type="button"
            @click="open = true"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="mobile-menu"
            class="ml-auto flex shrink-0 items-center px-4 text-accent transition-colors duration-200 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent sm:px-6 lg:hidden"
        >
            <span class="sr-only">{{ __('nav.open_menu') }}</span>
            <x-heroicon-o-bars-3 class="h-8 w-8" aria-hidden="true" />
        </button>
    </x-layout.container>
</div>
