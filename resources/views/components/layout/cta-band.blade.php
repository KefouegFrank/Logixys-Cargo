@php $locale = app()->getLocale(); @endphp

{{--
    Sits between the last section and the footer, not inside either. The backdrop
    fills only the lower half in footer navy, so the seam runs through the middle
    of the card and it straddles both — as in the reference.
--}}
<div class="relative">
    <div class="absolute inset-x-0 bottom-0 top-1/2 bg-navy-950" aria-hidden="true"></div>

    <x-layout.container class="relative">
        <div class="relative flex flex-col gap-6 overflow-hidden rounded-card bg-accent px-7 py-8 text-ink shadow-raised sm:px-10 lg:flex-row lg:items-center lg:justify-between lg:gap-8 lg:py-10">
            {{-- Geometric shapes ship pre-faded (alpha caps at 70/255), so they
                 need no extra opacity to sit quietly on the gold. --}}
            <img
                src="{{ asset('images/cta/shape-left.webp') }}"
                width="451" height="369" alt="" aria-hidden="true"
                class="pointer-events-none absolute inset-y-0 left-0 h-full w-auto select-none"
            >
            <img
                src="{{ asset('images/cta/shape-right.webp') }}"
                width="382" height="369" alt="" aria-hidden="true"
                class="pointer-events-none absolute inset-y-0 right-0 h-full w-auto select-none"
            >

            <div class="relative">
                <p class="flex items-center gap-2.5 font-heading text-xs font-bold uppercase tracking-[0.2em]">
                    <img src="{{ asset('images/title-marker-navy.png') }}" width="31" height="23" alt="" class="h-4 w-auto" aria-hidden="true">
                    {{ __('footer.cta_eyebrow') }}
                </p>
                <p class="mt-3 max-w-2xl font-heading text-2xl font-extrabold leading-tight [hyphens:none] sm:text-3xl">
                    {{ __('footer.cta_heading') }}
                </p>
            </div>

            {{-- A flex item rather than an absolute overlay, so it can never
                 collide with a longer heading in another language. --}}
            <img
                src="{{ asset('images/cta/arrow.webp') }}"
                width="227" height="73" alt="" aria-hidden="true"
                class="pointer-events-none relative hidden w-28 shrink-0 select-none lg:block xl:w-44"
            >

            <x-ui.button variant="navy" size="lg" :href="route('contact', ['locale' => $locale])" class="relative shrink-0 self-start lg:self-auto">
                {{ __('footer.cta_button') }}
                <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
            </x-ui.button>
        </div>
    </x-layout.container>
</div>
