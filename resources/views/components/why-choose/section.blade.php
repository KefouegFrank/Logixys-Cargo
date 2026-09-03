@php
    $reasons = config('why_choose.reasons');
    $stats = config('why_choose.stats');
@endphp

{{--
    White, bridging Process (dark) into the CTA band, whose straddle effect
    only reads if the section directly above it is light — see cta-band.blade.php.
--}}
<section class="bg-white py-16 lg:py-24" aria-labelledby="why-choose-heading">
    <x-layout.container>
        <div class="mx-auto max-w-2xl text-center">
            <x-ui.eyebrow class="mx-auto w-fit">{{ __('why_choose.eyebrow') }}</x-ui.eyebrow>

            <h2 id="why-choose-heading" class="mt-5 font-heading text-3xl font-extrabold leading-tight text-ink sm:text-4xl">
                {{ __('why_choose.heading_before') }}
                <span class="bg-[linear-gradient(to_top,var(--color-gold-400)_0.2em,transparent_0.2em)] px-0.5">{{ __('why_choose.heading_highlight') }}</span>
                {{ __('why_choose.heading_after') }}
            </h2>

            <p class="mt-4 text-base leading-relaxed text-ink-muted">{{ __('why_choose.intro') }}</p>
        </div>

        {{-- Two containers side by side from lg: reasons stacked on the left,
             stats in a fixed 2x2 grid on the right. Stacks below lg. --}}
        <div class="mt-14 grid gap-10 lg:grid-cols-2 lg:gap-12">
            <div class="space-y-5">
                @foreach ($reasons as $reason)
                    <x-why-choose.reason :reason="$reason" />
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-5">
                @foreach ($stats as $stat)
                    <x-why-choose.stat :stat="$stat" />
                @endforeach
            </div>
        </div>
    </x-layout.container>
</section>
