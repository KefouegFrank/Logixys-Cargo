@php
    $services = config('service_cards');
    $locale = app()->getLocale();
    $servicesUrl = route('services', ['locale' => $locale]);
@endphp

{{--
    Grid from xl up, where all four fit at a readable width. Below that it
    becomes a scroll-snap carousel — two per view on tablet, one on mobile —
    rather than a four-row stack.
--}}
<section class="bg-white py-16 lg:py-24" aria-labelledby="services-heading" x-data="cardScroller">
    <x-layout.container>
        <div class="flex flex-col gap-8 xl:flex-row xl:items-center xl:justify-between">
            <div class="max-w-xl">
                {{-- Chip is on the marker only. Gold fill with a navy marker keeps
                     the true brand gold here and still reads at 10.2:1. --}}
                <p class="flex items-center gap-3 font-heading text-sm font-bold uppercase tracking-[0.2em] text-ink">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-field bg-accent">
                        <img src="{{ asset('images/title-marker-navy.png') }}" width="31" height="23" alt="" class="h-4 w-auto" aria-hidden="true">
                    </span>
                    {{ __('services.eyebrow') }}
                </p>

                {{-- Highlight is a gold underline swipe, not gold text: the brand
                     gold is decorative here, so the word keeps navy's contrast. --}}
                <h2 id="services-heading" class="mt-5 font-heading text-3xl font-extrabold leading-tight text-ink sm:text-4xl">
                    {{ __('services.heading_before') }}
                    <span class="bg-[linear-gradient(to_top,var(--color-gold-400)_0.2em,transparent_0.2em)] px-0.5">{{ __('services.heading_highlight') }}</span>
                    {{ __('services.heading_after') }}
                </h2>

                <p class="mt-4 text-base leading-relaxed text-ink-muted">
                    {{ __('services.intro') }}
                </p>

                {{-- Carousel arrows: under the paragraph, right-aligned, below xl only. --}}
                <div class="mt-6 flex justify-end gap-3 xl:hidden">
                    <button
                        type="button"
                        @click="page(-1)"
                        :disabled="atStart"
                        aria-label="{{ __('services.prev') }}"
                        class="flex h-11 w-11 items-center justify-center rounded-pill border border-line text-ink transition-colors duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus disabled:pointer-events-none disabled:opacity-40"
                    >
                        <x-heroicon-m-arrow-left class="h-5 w-5" aria-hidden="true" />
                    </button>

                    <button
                        type="button"
                        @click="page(1)"
                        :disabled="atEnd"
                        aria-label="{{ __('services.next') }}"
                        class="flex h-11 w-11 items-center justify-center rounded-pill border border-line text-ink transition-colors duration-200 hover:border-navy-900 hover:bg-navy-900 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus disabled:pointer-events-none disabled:opacity-40"
                    >
                        <x-heroicon-m-arrow-right class="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>
            </div>

            <div class="hidden shrink-0 xl:block">
                <x-ui.button variant="accent" size="lg" :href="$servicesUrl" class="whitespace-nowrap">
                    {{ __('services.view_all') }}
                    <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                </x-ui.button>
            </div>
        </div>

        {{-- One track: flex + snap below xl, plain grid above it. --}}
        <div
            x-ref="track"
            @scroll.passive="sync()"
            class="no-scrollbar mt-10 flex snap-x snap-mandatory gap-6 overflow-x-auto pb-3 xl:mt-12 xl:grid xl:grid-cols-4 xl:overflow-visible xl:pb-0"
        >
            @foreach ($services as $i => $service)
                <x-services.card
                    :service="$service"
                    :index="$i"
                    class="w-full shrink-0 snap-start sm:w-[calc(50%-0.75rem)] xl:w-auto xl:shrink"
                />
            @endforeach
        </div>

        {{-- Below xl the CTA sits under the cards instead of beside the heading. --}}
        <div class="mt-10 xl:hidden">
            <x-ui.button
                variant="accent"
                size="lg"
                :href="$servicesUrl"
                class="w-full justify-center sm:w-auto"
            >
                {{ __('services.view_all') }}
                <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
            </x-ui.button>
        </div>
    </x-layout.container>
</section>
