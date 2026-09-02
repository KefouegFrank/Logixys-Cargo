@props([
    'variant' => 'accent',
    'href' => null,
    'size' => 'md',
])

@php
    /*
     * Hover inverts the pair rather than nudging one shade, so background and
     * text both travel. Every inverted pair is gold/navy or white/navy, which
     * stays at 10.2:1 or better.
     */
    $variants = [
        'accent' => 'bg-accent text-ink hover:bg-navy-900 hover:text-accent',
        'light' => 'bg-white text-ink hover:bg-navy-900 hover:text-white',
        'navy' => 'bg-navy-900 text-white hover:bg-accent hover:text-ink',
        'outline' => 'border-2 border-current text-ink hover:bg-ink hover:text-white',
    ];

    $sizes = [
        'sm' => 'gap-1.5 px-3.5 py-2 text-xs',
        'md' => 'gap-2 px-5 py-2.5 text-sm',
        'lg' => 'gap-2.5 px-7 py-3.5 text-base',
    ];

    $classes = trim(implode(' ', [
        'inline-flex items-center justify-center rounded-field font-heading font-bold',
        'transition-colors duration-200 ease-out',
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus',
        // Any icon in the slot drifts toward the label's reading direction on hover.
        '[&_svg]:transition-transform [&_svg]:duration-300 [&_svg]:ease-smooth hover:[&_svg]:translate-x-1',
        $variants[$variant] ?? $variants['accent'],
        $sizes[$size] ?? $sizes['md'],
    ]));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->class($classes)->merge(['type' => 'button']) }}>{{ $slot }}</button>
@endif
