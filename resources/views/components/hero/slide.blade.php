@props(['slide', 'index'])

@php
    $t = "hero.slides.{$slide['key']}";
    $widths = config('hero.widths');
    $srcset = collect($widths)
        ->map(fn ($w) => asset("images/hero/{$slide['image']}-{$w}.webp")." {$w}w")
        ->implode(', ');
    $sizes = '100vw';
@endphp

{{-- Image layer. Only the first is eager: the rest are below the fold in every
     sense that matters, and racing them would cost the LCP. --}}
<picture>
    <source type="image/webp" srcset="{{ $srcset }}" sizes="{{ $sizes }}">
    <img
        src="{{ asset("images/hero/{$slide['image']}-1280.jpg") }}"
        alt="{{ __("{$t}.alt") }}"
        width="1920"
        height="980"
        @if ($index === 0)
            fetchpriority="high" decoding="async"
        @else
            loading="lazy" decoding="async"
        @endif
        x-show="active === {{ $index }}"
        x-transition:enter="transition-opacity ease-out duration-700"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-700"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        :class="active === {{ $index }} && ! reduced && 'motion-safe:animate-hero-pan'"
        class="absolute inset-0 h-full w-full object-cover"
    >
</picture>
