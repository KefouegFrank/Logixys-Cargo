{{-- Avatar + "need help" phone link. Shared by the home and about-page intro sections. --}}
<a href="tel:{{ config('brand.contact.phone_href') }}" {{ $attributes->class('group flex items-center gap-3') }}>
    <picture>
        <source type="image/webp" srcset="{{ asset('images/about/avatar.webp') }}">
        <img
            src="{{ asset('images/about/avatar.jpg') }}"
            alt="{{ __('about.alt.avatar') }}"
            width="60" height="60" loading="lazy" decoding="async"
            class="h-12 w-12 rounded-pill object-cover ring-2 ring-accent"
        >
    </picture>
    <span>
        <span class="block text-xs text-ink-muted">{{ __('about.need_help') }}</span>
        <span class="block font-heading text-base font-bold text-ink transition-colors duration-200 group-hover:text-navy-700">
            {{ config('brand.contact.phone') }}
        </span>
    </span>
</a>
