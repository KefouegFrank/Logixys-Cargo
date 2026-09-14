@php
    $services = config('service_cards');
    $checklist = config('about.checklist');
@endphp

<section class="bg-navy-950 py-16 lg:py-24" aria-labelledby="about-capabilities-heading">
    <x-layout.container>
        <div class="grid items-center gap-14 lg:grid-cols-2 lg:gap-16">
            <div>
                <x-ui.eyebrow dark>{{ __('about.capabilities.eyebrow') }}</x-ui.eyebrow>

                <h2 id="about-capabilities-heading" class="mt-5 font-heading text-3xl font-extrabold leading-tight text-white sm:text-4xl">
                    {{ __('about.capabilities.heading') }}
                </h2>

                <p class="mt-5 text-base leading-relaxed text-white/70">{{ __('about.capabilities.body') }}</p>

                <ul class="mt-8 grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                    @foreach ($checklist as $item)
                        <li class="flex items-center gap-2.5 text-sm font-semibold text-white">
                            <x-heroicon-s-check-circle class="h-5 w-5 shrink-0 text-accent" aria-hidden="true" />
                            {{ __("about.capabilities.checklist.{$item}") }}
                        </li>
                    @endforeach
                </ul>

                <div class="mt-10">
                    <x-ui.button variant="accent-on-navy" size="lg" :href="route('services', ['locale' => app()->getLocale()])">
                        {{ __('about.cta_services') }}
                        <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
                    </x-ui.button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                @foreach ($services as $i => $service)
                    <div class="{{ $i % 2 === 1 ? 'mt-8' : '' }}">
                        <x-about.capability-card :service="$service" />
                    </div>
                @endforeach
            </div>
        </div>
    </x-layout.container>
</section>
