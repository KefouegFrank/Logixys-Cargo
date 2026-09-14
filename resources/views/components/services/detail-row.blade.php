@props(['service', 'index'])

@php
    $key = $service['type']->value;
    $reversed = $index % 2 === 1;
    $features = __("services.features.{$key}");
@endphp

<div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
    <div class="{{ $reversed ? 'lg:order-2' : '' }}">
        <picture>
            <source type="image/webp" srcset="{{ asset("images/services/{$service['image']}.webp") }}">
            <img
                src="{{ asset("images/services/{$service['image']}.jpg") }}"
                alt="{{ __("services.alt.{$key}") }}"
                loading="lazy"
                decoding="async"
                class="aspect-[4/3] w-full rounded-card object-cover shadow-raised"
            >
        </picture>
    </div>

    <div class="{{ $reversed ? 'lg:order-1' : '' }}">
        <span class="flex h-14 w-14 items-center justify-center rounded-card bg-accent text-ink">
            <x-icon.service :name="$service['icon']" class="h-7 w-7" />
        </span>

        <h3 class="mt-5 font-heading text-2xl font-extrabold leading-tight text-ink sm:text-3xl">
            {{ $service['type']->label() }}
        </h3>

        <p class="mt-4 text-base leading-relaxed text-ink-muted">
            {{ __("services.items.{$key}") }}
        </p>

        <ul class="mt-6 space-y-3">
            @foreach ($features as $feature)
                <li class="flex items-center gap-2.5 text-sm font-semibold text-ink">
                    <x-heroicon-s-check-circle class="h-5 w-5 shrink-0 text-navy-700" aria-hidden="true" />
                    {{ $feature }}
                </li>
            @endforeach
        </ul>

        <x-ui.button variant="navy" size="md" class="mt-7" :href="route('contact', ['locale' => app()->getLocale()])">
            {{ __('footer.cta_button') }}
            <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
        </x-ui.button>
    </div>
</div>
