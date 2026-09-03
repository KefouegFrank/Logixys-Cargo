@php
    $locale = app()->getLocale();
    $features = config('about.features');
@endphp

{{-- bg.png is a near-white chevron texture; the white base keeps it subtle
     if the image ever fails to load. --}}
<section
    class="relative overflow-hidden bg-white bg-cover bg-center bg-no-repeat py-16 lg:py-24"
    style="background-image: url('{{ asset('images/about/bg.webp') }}')"
    aria-labelledby="about-heading"
>
    <x-layout.container>
        <div class="grid items-center gap-14 lg:grid-cols-[1.12fr_1fr] lg:gap-12">

            {{--
                Image composition. Every piece is placed as a percentage inside a
                fixed-ratio box, so the arrangement keeps the reference's exact
                proportions at any width rather than drifting as a flex row does.
                Hidden below lg: there is no room for it to read at that size.
            --}}
            <div class="relative hidden aspect-[66/65] w-full max-w-xl lg:block">
                {{-- Gold plate, peeking out left and above the first photo. --}}
                <span class="absolute left-[3%] top-[5.4%] h-[77.7%] w-[25.8%] bg-accent" aria-hidden="true"></span>

                <picture class="absolute left-[9.4%] top-[10.5%] h-[69.2%] w-[42.9%]">
                    <source type="image/webp" srcset="{{ asset('images/about/containers.webp') }}">
                    <img
                        src="{{ asset('images/about/containers.jpg') }}"
                        alt="{{ __('about.alt.containers') }}"
                        width="340" height="500" loading="lazy" decoding="async"
                        class="h-full w-full border-[7px] border-white object-cover shadow-card"
                    >
                </picture>

                {{-- Sits behind the next photo: only its top arc clears the photo edge. --}}
                <img
                    src="{{ asset('images/about/shape.webp') }}"
                    width="267" height="299" alt="" aria-hidden="true"
                    class="pointer-events-none absolute left-[57%] top-[12%] w-[19%]"
                >

                <picture class="absolute left-[52.3%] top-[23.1%] h-[73.8%] w-[46.2%]">
                    <source type="image/webp" srcset="{{ asset('images/about/team.webp') }}">
                    <img
                        src="{{ asset('images/about/team.jpg') }}"
                        alt="{{ __('about.alt.team') }}"
                        width="330" height="500" loading="lazy" decoding="async"
                        class="h-full w-full object-cover shadow-raised"
                    >
                </picture>

                {{-- Badge straddles the seam between the two photos. --}}
                <x-about.badge class="absolute left-[53.8%] top-[71.5%] z-30 w-[30%] -translate-x-1/2 -translate-y-1/2" />
            </div>

            {{-- Content --}}
            <div>
                <p class="flex items-center gap-3 font-heading text-base font-bold uppercase tracking-[0.2em] text-ink">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-field bg-accent">
                        <img src="{{ asset('images/title-marker-navy.png') }}" width="31" height="23" alt="" class="h-4 w-auto" aria-hidden="true">
                    </span>
                    {{ __('about.eyebrow') }}
                </p>

                <h2 id="about-heading" class="mt-5 font-heading text-3xl font-extrabold leading-tight text-ink sm:text-4xl">
                    {{ __('about.heading_before') }}
                    <span class="bg-[linear-gradient(to_top,var(--color-gold-400)_0.2em,transparent_0.2em)] px-0.5">{{ __('about.heading_highlight') }}</span>
                    {{ __('about.heading_after') }}
                </h2>

                <p class="mt-5 text-base leading-relaxed text-ink-muted">{{ __('about.body') }}</p>

                <div class="mt-9 space-y-7">
                    @foreach ($features as $feature)
                        <x-about.feature :feature="$feature" />
                    @endforeach
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-5">
                    <x-ui.button variant="accent" size="lg" :href="route('about', ['locale' => $locale])">
                        {{ __('about.cta') }}
                        <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                    </x-ui.button>

                    <a href="tel:{{ config('brand.contact.phone_href') }}" class="group flex items-center gap-3">
                        <picture>
                            <source type="image/webp" srcset="{{ asset('images/about/avatar.webp') }}">
                            <img
                                src="{{ asset('images/about/avatar.jpg') }}"
                                alt="{{ __('about.alt.avatar') }}"
                                width="60" height="60" loading="lazy" decoding="async"
                                class="h-12 w-12 rounded-pill object-cover ring-2 ring-accent"
                            >
                        </picture>
                        <span>
                            <span class="block text-xs text-ink-muted">{{ __('about.need_help') }}</span>
                            <span class="block font-heading text-base font-bold text-ink transition-colors duration-200 group-hover:text-navy-700">
                                {{ config('brand.contact.phone') }}
                            </span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </x-layout.container>
</section>
