{{--
    Appears once the visitor is well down the page. Uses the same reduced-motion
    check as the carousels so the jump isn't animated for those who opt out.
--}}
<button
    type="button"
    x-data="{ shown: false }"
    x-init="shown = window.scrollY > 600"
    @scroll.window.throttle.200ms.passive="shown = window.scrollY > 600"
    x-show="shown"
    x-cloak
    x-transition:enter="transition ease-smooth duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    @click="window.scrollTo({ top: 0, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' })"
    aria-label="{{ __('footer.back_to_top') }}"
    class="fixed bottom-16 right-6 z-40 flex h-12 w-12 items-center justify-center rounded-field bg-accent text-ink shadow-raised transition-colors duration-200 hover:bg-navy-900 hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus"
>
    <x-heroicon-m-arrow-up class="h-5 w-5" aria-hidden="true" />
</button>
