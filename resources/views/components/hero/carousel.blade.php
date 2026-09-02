@php
    $slides = config('hero.slides');
    $interval = config('hero.interval');
    $locale = app()->getLocale();
    $first = $slides[0];
    $firstSrcset = collect(config('hero.widths'))
        ->map(fn ($w) => asset("images/hero/{$first['image']}-{$w}.webp")." {$w}w")
        ->implode(', ');
@endphp

{{-- The first slide is the LCP element, so it gets a real preload rather than
     waiting on the parser to reach the <picture>. --}}
@push('head')
    <link rel="preload" as="image" type="image/webp" imagesrcset="{{ $firstSrcset }}" imagesizes="100vw" fetchpriority="high">
@endpush

<section
    x-data="heroCarousel({{ count($slides) }}, {{ $interval }})"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    @focusin="paused = true"
    @focusout="paused = false"
    @touchstart.passive="onTouchStart($event)"
    @touchend.passive="onTouchEnd($event)"
    @keydown.window.left="prev()"
    @keydown.window.right="next()"
    role="region"
    aria-roledescription="carousel"
    aria-label="{{ __('hero.carousel') }}"
    class="relative isolate min-h-[34rem] overflow-hidden bg-navy-950 lg:h-[calc(100svh-7.5rem)] lg:max-h-[46rem] lg:min-h-[38rem]"
>
    @foreach ($slides as $i => $slide)
        <x-hero.slide :slide="$slide" :index="$i" />
    @endforeach

    {{-- Scrim. Heavier on the left so the copy keeps its contrast whatever the
         photo does; the bottom fade seats the section against the page. --}}
    <div class="absolute inset-0 bg-gradient-to-r from-navy-950/92 via-navy-950/70 to-navy-950/25" aria-hidden="true"></div>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-navy-950/70 to-transparent" aria-hidden="true"></div>

    {{-- Watermark: the service word, oversized and outlined, bottom right. --}}
    <div class="pointer-events-none absolute bottom-4 right-6 z-0 hidden max-w-[60%] select-none overflow-hidden xl:block" aria-hidden="true">
        @foreach ($slides as $i => $slide)
            <p
                x-show="active === {{ $i }}"
                x-transition:enter="transition-opacity duration-700"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="text-outline whitespace-nowrap font-heading text-[5rem] font-extrabold uppercase leading-none tracking-[0.12em] 2xl:text-[6.5rem]"
            >{{ __("hero.slides.{$slide['key']}.watermark") }}</p>
        @endforeach
    </div>

    <x-hero.pagination :slides="$slides" />
    <x-hero.controls />

    {{-- Content. Slides are stacked in one grid cell so the section never
         jumps height when copy length changes between languages. --}}
    <x-layout.container class="relative z-10 flex h-full flex-col justify-center py-14 lg:py-0 lg:pl-24">
        <div class="grid max-w-2xl">
            @foreach ($slides as $i => $slide)
                @php $t = "hero.slides.{$slide['key']}"; @endphp
                <div
                    class="[grid-area:1/1]"
                    x-show="active === {{ $i }}"
                    x-transition:enter="transition ease-smooth duration-500 delay-150"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    :aria-hidden="active !== {{ $i }}"
                >
                    <p class="font-heading text-xs font-bold uppercase tracking-[0.25em] text-accent sm:text-sm">
                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}. {{ __("{$t}.eyebrow") }}
                    </p>

                    <h1 class="mt-4 font-heading text-[2.1rem] font-extrabold leading-[1.08] text-white sm:text-5xl xl:text-6xl">
                        {{ __("{$t}.before") }}
                        <span class="text-accent">{{ __("{$t}.highlight") }}</span>
                        {{ __("{$t}.after") }}
                    </h1>

                    <p class="mt-5 max-w-lg text-sm leading-relaxed text-white/80 sm:text-base">
                        {{ __("{$t}.body") }}
                    </p>

                    <x-ui.button
                        variant="accent"
                        size="lg"
                        :href="route($slide['route'], ['locale' => $locale])"
                        class="mt-7"
                        ::tabindex="active === {{ $i }} ? 0 : -1"
                    >
                        {{ __("{$t}.cta") }}
                        <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                    </x-ui.button>
                </div>
            @endforeach
        </div>

        <x-hero.tracking-form class="mt-10" />
    </x-layout.container>

    {{-- Live region for assistive tech; the visual slide change is silent. --}}
    <p class="sr-only" aria-live="polite" x-text="`{{ __('hero.slide_of', ['current' => '__C__', 'total' => count($slides)]) }}`.replace('__C__', active + 1)"></p>

    {{-- Autoplay progress. Hidden once the visitor takes over. --}}
    <div class="absolute inset-x-0 bottom-0 z-20 h-1 bg-white/10" x-show="! stopped" x-cloak aria-hidden="true">
        <div class="h-full bg-accent" :style="`width: ${progress}%`"></div>
    </div>
</section>
