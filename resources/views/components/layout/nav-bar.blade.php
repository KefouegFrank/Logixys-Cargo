{{-- Grid cell (row 2, col 2) inside header's lg:grid — see header.blade.php.
     Navy, not gold: gold as a full-width fill here made it read as the site's
     dominant color, when the logo is 77% navy / 23% gold. Gold now shows up
     only as the accent it's meant to be — the CTA button, the active
     underline, the flag. Deliberately indented further than the top bar
     above it (pl-14 vs its pl-8). --}}
<div class="flex items-stretch bg-navy-900 pl-14 content-edge">
    <nav class="flex flex-1 items-center gap-5 xl:gap-9" aria-label="{{ __('nav.main_nav') }}">
        @foreach (config('navigation.main') as $item)
            <x-nav.item :route="$item['route']" :label="$item['label']" />
        @endforeach
    </nav>

    <div class="ml-auto flex shrink-0 items-center gap-1 xl:gap-3">
        <x-nav.locale-switcher />

        <x-ui.button
            variant="accent-on-navy"
            :href="route('tracking.index', ['locale' => app()->getLocale()])"
            class="whitespace-nowrap"
        >
            {{ __('nav.track_goods') }}
            {{-- <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" /> --}}
        </x-ui.button>
    </div>
</div>
