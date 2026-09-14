@php
    $items = __('services.faq');
@endphp

<section class="bg-white py-16 lg:py-24" aria-labelledby="services-faq-heading" x-data="{ open: 0 }">
    <x-layout.container>
        <div class="mx-auto max-w-2xl text-center">
            <x-ui.eyebrow class="mx-auto w-fit">{{ __('services.faq_eyebrow') }}</x-ui.eyebrow>

            <h2 id="services-faq-heading" class="mt-5 font-heading text-3xl font-extrabold leading-tight text-ink sm:text-4xl">
                {{ __('services.faq_heading') }}
            </h2>
        </div>

        <div class="mx-auto mt-12 max-w-3xl divide-y divide-line">
            @foreach ($items as $i => $item)
                <div class="py-5">
                    <button
                        type="button"
                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                        :aria-expanded="open === {{ $i }}"
                        aria-controls="services-faq-{{ $i }}"
                        class="flex w-full items-center justify-between gap-4 text-left"
                    >
                        <span class="font-heading text-base font-bold text-ink sm:text-lg">{{ $item['q'] }}</span>

                        <x-heroicon-m-chevron-down
                            class="h-5 w-5 shrink-0 text-navy-700 transition-transform duration-300 ease-smooth"
                            x-bind:class="open === {{ $i }} ? 'rotate-180' : ''"
                            aria-hidden="true"
                        />
                    </button>

                    {{-- A 0fr/1fr grid row, not x-show: x-show swaps display instantly, so
                         the row below jumps the moment it fires while the fade is still
                         catching up. Animating the row's own height keeps it one smooth
                         motion. overflow-hidden on the inner div is what lets the row
                         actually collapse to 0 instead of clamping at its content size. --}}
                    <div
                        id="services-faq-{{ $i }}"
                        class="grid transition-[grid-template-rows] duration-300 ease-smooth {{ $i === 0 ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]' }}"
                        x-bind:class="open === {{ $i }} ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'"
                    >
                        <div class="overflow-hidden">
                            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-ink-muted">{{ $item['a'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-layout.container>
</section>
