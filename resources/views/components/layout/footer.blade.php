@php
    $locale = app()->getLocale();
    $services = config('service_cards');

    $quickLinks = [
        ['route' => 'home', 'label' => 'nav.home'],
        ['route' => 'about', 'label' => 'nav.about'],
        ['route' => 'services', 'label' => 'nav.services'],
        ['route' => 'tracking.index', 'label' => 'nav.track_goods'],
        ['route' => 'contact', 'label' => 'nav.contact'],
    ];

    $legalLinks = [
        ['route' => 'legal.notice', 'label' => 'legal.notice.title'],
        ['route' => 'legal.privacy', 'label' => 'legal.privacy.title'],
        ['route' => 'legal.terms', 'label' => 'legal.terms.title'],
    ];
@endphp

<footer class="relative bg-navy-950 text-navy-200">
    <x-layout.container>
        <div class="grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8 lg:py-16">
            {{-- Brand --}}
            <div>
                <x-brand.logo variant="light" class="h-10" />
                <p class="mt-5 max-w-xs text-base leading-relaxed text-navy-300">{{ __('footer.blurb') }}</p>
            </div>

            <x-layout.footer-column :title="__('footer.quick_links')">
                @foreach ($quickLinks as $link)
                    <li>
                        <a href="{{ route($link['route'], ['locale' => $locale]) }}" class="inline-flex items-center gap-2 py-1 text-base transition-colors duration-200 hover:text-accent">
                            <x-heroicon-m-chevron-right class="h-3.5 w-3.5 text-accent" aria-hidden="true" />
                            {{ __($link['label']) }}
                        </a>
                    </li>
                @endforeach
            </x-layout.footer-column>

            <x-layout.footer-column :title="__('footer.our_services')">
                @foreach ($services as $service)
                    <li>
                        <a href="{{ route('services', ['locale' => $locale]) }}" class="inline-flex items-center gap-2 py-1 text-base transition-colors duration-200 hover:text-accent">
                            <x-heroicon-m-chevron-right class="h-3.5 w-3.5 text-accent" aria-hidden="true" />
                            {{ $service['type']->label() }}
                        </a>
                    </li>
                @endforeach
            </x-layout.footer-column>

            <x-layout.footer-column :title="__('footer.contact')">
                <li class="flex gap-3 py-1">
                    <x-heroicon-o-map-pin class="mt-0.5 h-4 w-4 shrink-0 text-accent" aria-hidden="true" />
                    <span class="text-sm">{{ config('brand.contact.address') }}</span>
                </li>
                <li>
                    <a href="mailto:{{ config('brand.contact.email') }}" class="flex gap-3 py-1 text-base transition-colors duration-200 hover:text-accent">
                        <x-heroicon-o-envelope class="mt-0.5 h-4 w-4 shrink-0 text-accent" aria-hidden="true" />
                        <span class="break-all">{{ config('brand.contact.email') }}</span>
                    </a>
                </li>
                <li>
                    <a href="tel:{{ config('brand.contact.phone_href') }}" class="flex gap-3 py-1 text-base transition-colors duration-200 hover:text-accent">
                        <x-heroicon-o-phone class="mt-0.5 h-4 w-4 shrink-0 text-accent" aria-hidden="true" />
                        <span>{{ config('brand.contact.phone') }}</span>
                    </a>
                </li>
                <li class="flex gap-3 py-1">
                    <x-heroicon-o-clock class="mt-0.5 h-4 w-4 shrink-0 text-accent" aria-hidden="true" />
                    <span class="text-sm">
                        {{ config('brand.contact.hours_weekday') }}<br>
                        {{ config('brand.contact.hours_weekend') }}
                    </span>
                </li>
            </x-layout.footer-column>
        </div>
    </x-layout.container>

    {{-- Bottom bar --}}
    <div class="border-t border-white/10">
        <x-layout.container class="flex flex-col items-center gap-4 pb-24 pt-6 text-xs text-navy-300 sm:flex-row sm:justify-between sm:pb-6">
            <p>&copy; {{ now()->year }} {{ config('app.name') }}. {{ __('footer.rights') }}</p>

            <ul class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2">
                @foreach ($legalLinks as $link)
                    <li>
                        <a href="{{ route($link['route'], ['locale' => $locale]) }}" class="transition-colors duration-200 hover:text-accent">
                            {{ __($link['label']) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </x-layout.container>
    </div>

    <x-layout.back-to-top />
</footer>
