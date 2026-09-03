{{--
    Circular badge: the ring of text rotates, the navy centre stays put. Text is
    set on an SVG path so it genuinely curves. Sizing comes from the caller so
    the badge can scale with the composition it sits in.
--}}
<div {{ $attributes->class('relative flex aspect-square items-center justify-center rounded-pill bg-accent shadow-raised') }}>
    <svg
        viewBox="0 0 200 200"
        class="absolute inset-0 h-full w-full animate-badge-spin text-ink"
        aria-hidden="true"
    >
        <defs>
            <path id="about-badge-path" fill="none" d="M100,100 m-74,0 a74,74 0 1,1 148,0 a74,74 0 1,1 -148,0" />
        </defs>
        <text class="font-heading text-[17px] font-bold uppercase" fill="currentColor" letter-spacing="1.1">
            <textPath href="#about-badge-path" startOffset="0">{{ __('about.badge') }}</textPath>
        </text>
    </svg>

    <span class="flex aspect-square w-[46%] items-center justify-center rounded-pill bg-navy-900">
        <img src="{{ asset('images/title-marker.png') }}" width="31" height="23" alt="" class="w-[42%]" aria-hidden="true">
    </span>
</div>
