@props(['step', 'index', 'last' => false])

@php $t = "process.steps.{$step['key']}"; @endphp

<div class="group relative text-center">
    <div class="relative inline-flex h-24 w-24 items-center justify-center rounded-pill border-2 border-dashed border-accent/50 transition-colors duration-300 group-hover:border-accent">
        <span class="flex h-16 w-16 items-center justify-center rounded-pill bg-white text-ink transition-colors duration-300 group-hover:bg-navy-900 group-hover:text-accent">
            @switch($step['icon'])
                @case('document-text')
                    <x-heroicon-o-document-text class="h-7 w-7" aria-hidden="true" />
                    @break
                @case('clipboard')
                    <x-heroicon-o-clipboard-document-check class="h-7 w-7" aria-hidden="true" />
                    @break
                @case('truck')
                    <x-heroicon-o-truck class="h-7 w-7" aria-hidden="true" />
                    @break
                @default
                    <x-heroicon-o-map-pin class="h-7 w-7" aria-hidden="true" />
            @endswitch
        </span>

        {{-- Connector: the gap between this badge and the next, arcing up and
             over. Absent after the last step and hidden below lg, where the
             grid falls back to two columns and there's no "next" in a row. --}}
        @unless ($last)
            <img
                src="{{ asset('images/process/arrow-gold.webp') }}"
                width="227" height="73" alt="" aria-hidden="true"
                class="pointer-events-none absolute left-full top-1/2 hidden w-[7.5rem] -translate-y-1/2 select-none lg:block xl:w-40"
            >
        @endunless
    </div>

    <p class="mt-6 font-heading text-xs font-bold uppercase tracking-[0.2em] text-accent">
        {{ __('process.step_label', ['number' => $index + 1]) }}
    </p>
    <h3 class="mt-2 font-heading text-lg font-bold text-white">{{ __("{$t}.title") }}</h3>
    <p class="mx-auto mt-2.5 max-w-[16rem] text-sm leading-relaxed text-white/60">{{ __("{$t}.body") }}</p>
</div>
