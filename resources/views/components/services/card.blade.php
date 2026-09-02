@props(['service', 'index'])

@php
    $type = $service['type'];
    $key = $type->value;
    $href = route('services', ['locale' => app()->getLocale()]);
@endphp

<article {{ $attributes->class('group relative h-full') }}>
    {{-- Offset plate peeking out bottom-right; it takes the gold on hover. --}}
    <span
        class="absolute inset-0 translate-x-2.5 translate-y-2.5 rounded-card bg-navy-100 transition-colors duration-300 ease-smooth group-hover:bg-accent"
        aria-hidden="true"
    ></span>

    <div class="relative flex h-full flex-col overflow-hidden rounded-card bg-white ring-1 ring-line/60">
        <div class="relative aspect-[3/2] overflow-hidden">
            <picture>
                <source type="image/webp" srcset="{{ asset("images/services/{$service['image']}.webp") }}">
                <img
                    src="{{ asset("images/services/{$service['image']}.jpg") }}"
                    alt="{{ __("services.alt.{$key}") }}"
                    loading="lazy"
                    decoding="async"
                    class="h-full w-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105"
                >
            </picture>

            <span class="absolute bottom-0 right-0 flex h-14 w-14 items-center justify-center rounded-tl-card bg-accent text-ink transition-colors duration-300 group-hover:bg-navy-900 group-hover:text-accent">
                <x-icon.service :name="$service['icon']" class="h-7 w-7" />
            </span>
        </div>

        {{-- Ghost number sits against the card, not the body, so it clears the border. --}}
        <span class="pointer-events-none absolute bottom-4 right-5 font-heading text-4xl font-extrabold leading-none text-navy-100 transition-colors duration-300 group-hover:text-gold-200" aria-hidden="true">
            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
        </span>

        <div class="flex flex-1 flex-col border-l-2 border-accent p-6">
            <h3 class="font-heading text-lg font-bold text-ink">
                {{-- Stretched link: the whole card is the target, but only one link exists. --}}
                <a href="{{ $href }}" class="transition-colors duration-200 before:absolute before:inset-0 hover:text-navy-700">
                    {{ $type->label() }}
                </a>
            </h3>

            <p class="mt-2.5 flex-1 text-sm leading-relaxed text-ink-muted">
                {{ __("services.items.{$key}") }}
            </p>

            <span class="mt-5 inline-flex w-fit items-center gap-1.5 rounded-field border border-line px-4 py-2 font-heading text-xs font-bold text-ink transition-colors duration-200 group-hover:border-navy-900 group-hover:bg-navy-900 group-hover:text-white">
                {{ __('services.read_more') }}
                <x-heroicon-m-chevron-double-right class="h-3.5 w-3.5 transition-transform duration-300 ease-smooth group-hover:translate-x-0.5" aria-hidden="true" />
            </span>
        </div>
    </div>
</article>
