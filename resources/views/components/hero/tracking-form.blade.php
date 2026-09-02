{{--
    Persistent across slides on purpose — it sits outside the rotation so it
    never flickers or moves under a visitor mid-type. Submits to the existing
    tracking route, which normalises the number and redirects to the result.
--}}
<form
    method="GET"
    action="{{ route('tracking.index', ['locale' => app()->getLocale()]) }}"
    {{ $attributes->class('w-full max-w-xl') }}
>
    <div class="rounded-card bg-white/95 p-2 shadow-raised backdrop-blur-sm sm:p-2.5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <div class="flex flex-1 items-center gap-2.5 px-3">
                <x-heroicon-o-magnifying-glass class="h-5 w-5 shrink-0 text-ink-subtle" aria-hidden="true" />
                <label for="hero-tracking" class="sr-only">{{ __('hero.track.label') }}</label>
                <input
                    id="hero-tracking"
                    type="text"
                    name="number"
                    required
                    autocomplete="off"
                    spellcheck="false"
                    placeholder="{{ __('hero.track.placeholder') }}"
                    class="w-full bg-transparent py-2.5 font-sans text-sm text-ink placeholder:text-ink-subtle focus:outline-none sm:text-base"
                >
            </div>

            <x-ui.button type="submit" size="md" class="shrink-0 max-sm:w-full">
                {{ __('hero.track.submit') }}
                <x-heroicon-m-arrow-right class="h-4 w-4" aria-hidden="true" />
            </x-ui.button>
        </div>
    </div>

    <p class="mt-2.5 text-xs text-white/70">{{ __('hero.track.hint') }}</p>
</form>
