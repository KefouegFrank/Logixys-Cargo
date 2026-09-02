{{-- Prev/next, desktop only — mobile drives the carousel by swipe and dots. --}}
<div class="absolute bottom-8 right-6 z-20 hidden items-center gap-3 lg:flex xl:bottom-28 xl:right-10">
    <button
        type="button"
        @click="prev()"
        aria-label="{{ __('hero.prev') }}"
        class="group flex h-12 w-12 items-center justify-center rounded-pill border border-white/40 bg-white/10 text-white backdrop-blur-sm transition-colors duration-200 hover:border-accent hover:bg-accent hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
    >
        <x-heroicon-m-chevron-left class="h-5 w-5 transition-transform duration-300 ease-smooth group-hover:-translate-x-0.5" aria-hidden="true" />
    </button>

    <button
        type="button"
        @click="next()"
        aria-label="{{ __('hero.next') }}"
        class="group flex h-12 w-12 items-center justify-center rounded-pill border border-white/40 bg-white/10 text-white backdrop-blur-sm transition-colors duration-200 hover:border-accent hover:bg-accent hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
    >
        <x-heroicon-m-chevron-right class="h-5 w-5 transition-transform duration-300 ease-smooth group-hover:translate-x-0.5" aria-hidden="true" />
    </button>
</div>
