{{-- White card inside the navy panel: bold label on top, then icon tile beside the
     values — the same three-block stack as the reference. --}}
@props(['icon', 'label', 'tone' => 'accent'])

@php
    $tones = [
        'accent' => 'bg-accent text-ink',
        'navy' => 'bg-navy-900 text-white',
        'muted' => 'bg-navy-600 text-white',
    ];
@endphp

<div class="rounded-card bg-white p-5">
    <p class="font-heading text-sm font-bold text-ink">{{ $label }}</p>

    <div class="mt-3 flex items-start gap-4">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-field {{ $tones[$tone] ?? $tones['accent'] }}">
            <x-dynamic-component :component="$icon" class="h-5 w-5" aria-hidden="true" />
        </span>
        <div class="min-w-0 space-y-0.5 pt-0.5 text-sm text-ink-muted">{{ $slot }}</div>
    </div>
</div>
