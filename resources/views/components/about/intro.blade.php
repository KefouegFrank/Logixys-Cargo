@php
    $experience = collect(config('why_choose.stats'))->firstWhere('key', 'experience');
@endphp

<section class="bg-white py-16 min-[971px]:py-24" aria-labelledby="about-intro-heading">
    <x-layout.container>
        {{-- Custom 971px break rather than lg (1024). Copy stays first in the DOM,
             so it also stays on top when stacked; the photo follows, capped and
             short rather than full-bleed until the two-column layout takes over. --}}
        <div class="grid items-center gap-14 min-[971px]:grid-cols-2 min-[971px]:gap-16">
            {{-- min-[971px]:order-2 flips this back to the right on the wide layout
                 while staying first in the DOM, so the stacked view (copy on top,
                 photo below) is untouched. --}}
            <div class="min-[971px]:order-2">
                <x-ui.eyebrow>{{ __('about.intro.eyebrow') }}</x-ui.eyebrow>

                <h2 id="about-intro-heading" class="mt-5 font-heading text-3xl font-extrabold leading-tight text-ink sm:text-4xl">
                    {{ __('about.intro.heading') }}
                </h2>

                <p class="mt-5 text-base leading-relaxed text-ink-muted">{{ __('about.intro.body_1') }}</p>
                <p class="mt-4 text-base leading-relaxed text-ink-muted">{{ __('about.intro.body_2') }}</p>

                <div class="mt-9 flex flex-wrap items-center gap-x-8 gap-y-5">
                    <x-ui.button variant="navy" size="lg" :href="route('services', ['locale' => app()->getLocale()])">
                        {{ __('about.cta_services') }}
                        <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                    </x-ui.button>

                    <x-ui.phone-cta />
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-sm min-[971px]:order-1 min-[971px]:mx-0 min-[971px]:max-w-none">
                <picture>
                    <source type="image/webp" srcset="{{ asset('images/about/team.webp') }}">
                    <img
                        src="{{ asset('images/about/team.jpg') }}"
                        alt="{{ __('about.alt.team') }}"
                        width="330" height="500" loading="lazy" decoding="async"
                        class="aspect-[4/3] w-full rounded-card object-cover object-top shadow-raised min-[971px]:aspect-[33/40] min-[971px]:object-center"
                    >
                </picture>

                {{-- Square plate overlapping the photo's bottom-left corner, sized
                     and shaped to match the client's reference exactly rather than
                     the pill-padded shape the rest of the site uses for this stat. --}}
                <div class="absolute -bottom-5 -left-5 flex h-32 w-32 flex-col items-center justify-center bg-accent px-4 text-center shadow-raised sm:h-40 sm:w-40">
                    <p class="font-heading text-3xl font-extrabold leading-none text-ink sm:text-4xl">
                        <span x-data="statCounter({{ $experience['value'] }})" x-text="display"></span>{{ $experience['suffix'] }}
                    </p>
                    <p class="mt-1.5 text-sm font-bold leading-snug text-ink">
                        {{ __('why_choose.stats.experience') }}
                    </p>
                </div>
            </div>
        </div>
    </x-layout.container>
</section>
