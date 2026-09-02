{{--
    Grid cell (row 1, col 2) inside header's lg:grid — see header.blade.php.
    `scrolled` (from the ancestor header's x-data) collapses this row via
    max-height + padding + opacity together, so it shrinks to nothing rather
    than just clipping — the row-span-2 logo cell shrinks with it for free.
--}}
<div
    :class="scrolled ? 'max-h-0 !py-0 opacity-0' : 'max-h-16 py-4 opacity-100'"
    class="flex items-center gap-6 overflow-hidden bg-navy-950 pl-8 text-sm font-semibold text-navy-100 content-edge transition-[max-height,padding,opacity] duration-300 ease-smooth"
    <span class="flex items-center gap-2">
        <x-heroicon-o-map-pin class="h-4 w-4 shrink-0 text-accent" aria-hidden="true" />
        <span>{{ config('brand.contact.address') }}</span>
    </span>

    <span class="h-3.5 w-px bg-white/20" aria-hidden="true"></span>

    <a href="mailto:{{ config('brand.contact.email') }}" class="flex items-center gap-2 transition-colors duration-200 hover:text-accent">
        <x-heroicon-o-envelope class="h-4 w-4 shrink-0 text-accent" aria-hidden="true" />
        <span>{{ config('brand.contact.email') }}</span>
    </a>

    <span class="h-3.5 w-px bg-white/20" aria-hidden="true"></span>

    <a href="tel:{{ config('brand.contact.phone_href') }}" class="flex items-center gap-2 transition-colors duration-200 hover:text-accent">
        <x-heroicon-o-phone class="h-4 w-4 shrink-0 text-accent" aria-hidden="true" />
        <span>{{ config('brand.contact.phone') }}</span>
    </a>
</div>
