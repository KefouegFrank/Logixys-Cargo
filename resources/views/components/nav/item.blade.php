@props([
    'route',
    'label',
    'variant' => 'bar',
])

@php
    // Match child routes too, so /suivi/LGXY-1 still lights up its parent entry.
    $active = request()->routeIs($route) || request()->routeIs($route.'.*');
    $href = route($route, ['locale' => app()->getLocale()]);
@endphp

@if ($variant === 'drawer')
    {{-- The gold bar on the left grows in place of the desktop underline. --}}
    <a
        href="{{ $href }}"
        @if ($active) aria-current="page" @endif
        {{ $attributes->class([
            'relative block border-b border-line py-3.5 pl-5 pr-5 font-heading text-base font-bold text-ink',
            'transition-[background-color,padding] duration-200',
            'before:absolute before:inset-y-0 before:left-0 before:w-1 before:bg-accent before:origin-top',
            'before:transition-transform before:duration-300 before:ease-smooth',
            'bg-surface-sunken before:scale-y-100' => $active,
            'before:scale-y-0 hover:bg-surface-sunken hover:pl-6 hover:before:scale-y-100' => ! $active,
        ]) }}
    >{{ __($label) }}</a>
@else
    {{-- Underline is always present; hover wipes it in from the left, active pins it open. --}}
    <a
        href="{{ $href }}"
        @if ($active) aria-current="page" @endif
        {{ $attributes->class([
            'relative flex items-center whitespace-nowrap py-6 font-heading text-sm font-bold tracking-wide',
            'transition-colors duration-200',
            'after:absolute after:inset-x-0 after:bottom-4 after:h-0.5 after:origin-left after:bg-ink',
            'after:transition-transform after:duration-300 after:ease-smooth',
            'text-ink after:scale-x-100' => $active,
            'text-ink/70 after:scale-x-0 hover:text-ink hover:after:scale-x-100' => ! $active,
        ]) }}
    >{{ __($label) }}</a>
@endif
