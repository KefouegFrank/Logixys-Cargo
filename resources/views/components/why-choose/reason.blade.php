@props(['reason'])

@php $t = "why_choose.reasons.{$reason['key']}"; @endphp

{{-- Icon-left card: a gold diamond peeks from behind the navy circle,
     rotated square clipped by the circle in front — same "shape peeking
     out" language as the services card's offset plate, scaled down. --}}
<div class="group flex items-center gap-5 rounded-card bg-white p-5 shadow-card transition-[box-shadow,transform] duration-300 ease-smooth hover:-translate-y-0.5 hover:shadow-raised">
    <span class="relative flex h-16 w-16 shrink-0 items-center justify-center">
        <span class="absolute -left-1.5 -top-1.5 h-11 w-11 rotate-45 rounded-sm bg-accent/60" aria-hidden="true"></span>
        <span class="relative flex h-16 w-16 items-center justify-center rounded-pill bg-navy-900 text-accent">
            @switch($reason['icon'])
                @case('shield-check')
                    <x-heroicon-o-shield-check class="h-7 w-7" aria-hidden="true" />
                    @break
                @case('clock')
                    <x-heroicon-o-clock class="h-7 w-7" aria-hidden="true" />
                    @break
                @default
                    <x-heroicon-o-signal class="h-7 w-7" aria-hidden="true" />
            @endswitch
        </span>
    </span>

    <div>
        <h3 class="font-heading text-lg font-bold text-ink">{{ __("{$t}.title") }}</h3>
        <p class="mt-1.5 text-sm leading-relaxed text-ink-muted">{{ __("{$t}.body") }}</p>
    </div>
</div>
