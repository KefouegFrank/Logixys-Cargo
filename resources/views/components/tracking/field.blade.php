{{-- One label/value pair. Renders nothing when the value came out blank, so the grid
     never shows a heading with an em dash under it. --}}
@props(['label', 'wide' => false])

@php
    $value = trim($slot->toHtml());
    // French puts a space before the colon; nothing else does.
    $colon = app()->getLocale() === 'fr' ? ' :' : ':';
@endphp

@if ($value !== '')
    <div @class(['sm:col-span-2 lg:col-span-3' => $wide])>
        <dt class="font-semibold text-ink">{{ $label }}{{ $colon }}</dt>
        <dd class="mt-1 text-ink-muted">{!! $value !!}</dd>
    </div>
@endif
