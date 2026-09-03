@props(['stat'])

<div class="rounded-card border border-line bg-white p-6 text-center shadow-card">
    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-pill bg-navy-900 text-accent">
        @switch($stat['icon'])
            @case('trophy')
                <x-heroicon-o-trophy class="h-7 w-7" aria-hidden="true" />
                @break
            @case('cube')
                <x-heroicon-o-cube class="h-7 w-7" aria-hidden="true" />
                @break
            @case('user-group')
                <x-heroicon-o-user-group class="h-7 w-7" aria-hidden="true" />
                @break
            @default
                <x-heroicon-o-globe-alt class="h-7 w-7" aria-hidden="true" />
        @endswitch
    </span>

    <p class="mt-4 font-heading text-4xl font-extrabold text-ink">
        <span x-data="statCounter({{ $stat['value'] }})" x-text="display"></span>@if ($stat['suffix']){{ $stat['suffix'] }}@endif
    </p>

    <p class="mt-1.5 text-sm text-ink-muted">{{ __("why_choose.stats.{$stat['key']}") }}</p>
</div>
