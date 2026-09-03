@props(['title'])

{{-- Heading carries a short gold rule, echoing the section eyebrows. --}}
<div>
    <h2 class="relative pb-3 font-heading text-lg font-bold text-white after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-9 after:bg-accent">
        {{ $title }}
    </h2>

    <ul class="mt-5 space-y-1">
        {{ $slot }}
    </ul>
</div>
