{{-- Grid cell (row 2, col 2) inside header's lg:grid — see header.blade.php.
     Deliberately indented further than the top bar above it (pl-14 vs its pl-8). --}}
<div class="flex items-stretch bg-accent pl-14 content-edge">
    <nav class="flex flex-1 items-center gap-5 xl:gap-9" aria-label="{{ __('nav.main_nav') }}">
        @foreach (config('navigation.main') as $item)
            <x-nav.item :route="$item['route']" :label="$item['label']" />
        @endforeach
    </nav>

    <div class="ml-auto flex shrink-0 items-center gap-1 xl:gap-3">
        <x-nav.locale-switcher />

        <x-ui.button
            variant="light"
            :href="route('tracking.index', ['locale' => app()->getLocale()])"
            class="whitespace-nowrap"
        >
            {{ __('nav.track_goods') }}
            {{-- <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" /> --}}
        </x-ui.button>
    </div>
</div>
