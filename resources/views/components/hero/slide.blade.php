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
        {{-- Opacity rather than x-show: every layer stays in flow, so nothing
             reflows mid-change and the cross-fade has no gap. --}}
        :class="active === {{ $index }} ? 'opacity-100 motion-safe:animate-hero-pan' : 'opacity-0'"
        class="absolute inset-0 h-full w-full object-cover transition-opacity duration-700 ease-out"
    >
</picture>
