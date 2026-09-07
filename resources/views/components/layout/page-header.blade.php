@props(['title'])

@php
    $widths = [640, 960, 1280, 1920];
    $srcset = collect($widths)->map(fn ($w) => asset("images/page-header/bg-{$w}.webp")." {$w}w")->implode(', ');
@endphp

{{-- The banner atop Services/Contact/About — first thing below the fold on
     those pages, so the image is eager and preloaded rather than lazy. --}}
@push('head')
    <link rel="preload" as="image" type="image/webp" imagesrcset="{{ $srcset }}" imagesizes="100vw" fetchpriority="high">
@endpush

<div class="relative overflow-hidden bg-navy-950 py-16 lg:py-20">
    <picture>
        <source type="image/webp" srcset="{{ $srcset }}" sizes="100vw">
        <img
            src="{{ asset('images/page-header/bg-1280.jpg') }}"
            alt="" aria-hidden="true"
            fetchpriority="high" decoding="async"
            class="absolute inset-0 h-full w-full object-cover"
        >
    </picture>
    {{-- Heavy scrim: texture behind the title, not a photo to look at — same
         treatment as the Process section's background. --}}
    <div class="absolute inset-0 bg-navy-950/80" aria-hidden="true"></div>

    <x-layout.container class="relative text-center">
        <h1 class="font-heading text-3xl font-extrabold text-white sm:text-4xl">{{ $title }}</h1>

        <nav aria-label="{{ __('nav.breadcrumb') }}" class="mt-4 flex items-center justify-center gap-2 text-sm">
            <a
                href="{{ route('home', ['locale' => app()->getLocale()]) }}"
                class="text-white/70 transition-colors duration-200 hover:text-accent"
            >{{ __('nav.home') }}</a>
            <x-heroicon-m-arrow-right class="h-4 w-4 shrink-0 text-accent" aria-hidden="true" />
            <span class="font-semibold text-accent" aria-current="page">{{ $title }}</span>
        </nav>
    </x-layout.container>
</div>
