{{-- Desktop only; the phone number reappears inside the mobile drawer. --}}
<div class="hidden bg-navy-950 text-navy-100 lg:block">
    <x-layout.container class="flex items-center gap-6 py-2.5 text-xs">
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
    </x-layout.container>
</div>
