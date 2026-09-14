@props(['service'])

@php
    $key = $service['type']->value;
    $href = route('services', ['locale' => app()->getLocale()]);
@endphp

{{-- Image with a bottom scrim, not a card body — this is a glance grid, the
     full description lives on the Services page this links to. --}}
<div class="group relative overflow-hidden rounded-card">
    <picture>
        <source type="image/webp" srcset="{{ asset("images/services/{$service['image']}.webp") }}">
        <img
            src="{{ asset("images/services/{$service['image']}.jpg") }}"
            alt="{{ __("services.alt.{$key}") }}"
            loading="lazy"
            decoding="async"
            class="aspect-[4/3] w-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105"
        >
    </picture>

    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/85 via-navy-950/10 to-transparent" aria-hidden="true"></div>

    <div class="absolute inset-x-0 bottom-0 flex items-center gap-3 p-4">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-pill bg-accent text-ink transition-colors duration-300 group-hover:bg-white">
            <x-icon.service :name="$service['icon']" class="h-5 w-5" />
        </span>
        <a href="{{ $href }}" class="font-heading text-base font-bold text-white before:absolute before:inset-0 transition-colors duration-200 group-hover:text-accent">
            {{ $service['type']->label() }}
        </a>
    </div>
</div>
