@props(['feature'])

@php $t = "about.features.{$feature['key']}"; @endphp

{{-- Hovering anywhere on the row flips the icon and inverts its tile. --}}
<div class="group flex gap-5">
    <span class="[perspective:600px]">
        <span
            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-card bg-navy-50 text-ink ring-1 ring-line transition-[background-color,color,transform] duration-500 ease-smooth [transform-style:preserve-3d] group-hover:bg-navy-900 group-hover:text-accent group-hover:[transform:rotateY(180deg)]"
        >
            @if ($feature['icon'] === 'globe')
                <x-heroicon-o-globe-alt class="h-7 w-7" aria-hidden="true" />
            @else
                <x-heroicon-o-shield-check class="h-7 w-7" aria-hidden="true" />
            @endif
        </span>
    </span>

    <div>
        <h3 class="font-heading text-lg font-bold text-ink">{{ __("{$t}.title") }}</h3>
        <p class="mt-1.5 text-sm leading-relaxed text-ink-muted">{{ __("{$t}.body") }}</p>
    </div>
</div>
