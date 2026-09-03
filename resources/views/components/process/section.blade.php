@php
    $steps = config('process');
    $widths = [960, 1280, 1920];
    $srcset = collect($widths)->map(fn ($w) => asset("images/process/bg-{$w}.webp")." {$w}w")->implode(', ');
@endphp

<section class="relative overflow-hidden bg-navy-950 py-16 lg:py-24" aria-labelledby="process-heading">
    {{-- Decorative: the photo carries no information text doesn't already give. --}}
    <picture>
        <source type="image/webp" srcset="{{ $srcset }}" sizes="100vw">
        <img
            src="{{ asset('images/process/bg-1280.jpg') }}"
            alt="" aria-hidden="true"
            loading="lazy" decoding="async"
            class="absolute inset-0 h-full w-full object-cover"
        >
    </picture>
    {{-- Heavy scrim: this is texture behind the content, not a photo to look at. --}}
    <div class="absolute inset-0 bg-navy-950/90" aria-hidden="true"></div>

    <x-layout.container class="relative">
        <div class="mx-auto max-w-2xl text-center">
            <p class="inline-flex items-center gap-2.5 font-heading text-sm font-bold uppercase tracking-[0.2em] text-accent">
                <img src="{{ asset('images/title-marker.png') }}" width="31" height="23" alt="" class="h-4 w-auto" aria-hidden="true">
                {{ __('process.eyebrow') }}
            </p>

            <h2 id="process-heading" class="mt-5 font-heading text-3xl font-extrabold leading-tight text-white sm:text-4xl">
                {{ __('process.heading_before') }}
                <span class="text-accent">{{ __('process.heading_highlight') }}</span>
                {{ __('process.heading_after') }}
            </h2>

            <p class="mt-4 text-base leading-relaxed text-white/70">{{ __('process.intro') }}</p>
        </div>

        <ol class="mt-12 grid grid-cols-2 gap-x-6 gap-y-10 sm:mt-16 sm:gap-y-14 lg:grid-cols-4">
            @foreach ($steps as $i => $step)
                <li>
                    <x-process.step :step="$step" :index="$i" :last="$loop->last" />
                </li>
            @endforeach
        </ol>
    </x-layout.container>
</section>
