@props(['slides'])

{{-- Numbered rail, desktop only. The line above and below echoes the
     reference's scroll indicator without pretending to be a scrollbar. --}}
<div class="pointer-events-none absolute inset-y-0 left-0 z-20 hidden w-24 flex-col items-center justify-center gap-5 lg:flex">
    <span class="h-16 w-px bg-white/25" aria-hidden="true"></span>

    <div class="pointer-events-auto flex flex-col gap-3">
        @foreach ($slides as $i => $slide)
            <button
                type="button"
                @click="select({{ $i }})"
                :aria-current="active === {{ $i }} ? 'true' : 'false'"
                aria-label="{{ __('hero.goto', ['number' => $i + 1]) }}"
                class="flex h-9 w-9 items-center justify-center rounded-pill border font-heading text-sm font-bold transition-all duration-300 ease-smooth focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
                :class="active === {{ $i }}
                    ? 'border-accent bg-accent text-ink scale-110'
                    : 'border-white/40 text-white/70 hover:border-white hover:text-white'"
            >{{ $i + 1 }}</button>
        @endforeach
    </div>

    <span class="h-16 w-px bg-white/25" aria-hidden="true"></span>
</div>

{{-- Mobile: dots centred under the content. --}}
<div class="absolute inset-x-0 bottom-6 z-20 flex justify-center gap-2 lg:hidden">
    @foreach ($slides as $i => $slide)
        <button
            type="button"
            @click="select({{ $i }})"
            :aria-current="active === {{ $i }} ? 'true' : 'false'"
            aria-label="{{ __('hero.goto', ['number' => $i + 1]) }}"
            class="h-2 rounded-pill transition-all duration-300 ease-smooth focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
            :class="active === {{ $i }} ? 'w-8 bg-accent' : 'w-2 bg-white/50'"
        ></button>
    @endforeach
</div>
