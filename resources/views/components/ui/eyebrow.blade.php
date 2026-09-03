@props(['dark' => false])

{{--
    The small label above a section heading — Services, About and Process each
    had this markup inline; pulled out here rather than writing a fourth copy.
    Light sections get a gold chip housing the navy marker; on a dark section
    that chip would vanish, so it's the gold marker directly with gold text.
--}}
@if ($dark)
    <p {{ $attributes->class('inline-flex items-center gap-2.5 font-heading text-sm font-bold uppercase tracking-[0.2em] text-accent') }}>
        <img src="{{ asset('images/title-marker.png') }}" width="31" height="23" alt="" class="h-4 w-auto" aria-hidden="true">
        {{ $slot }}
    </p>
@else
    <p {{ $attributes->class('flex items-center gap-3 font-heading text-sm font-bold uppercase tracking-[0.2em] text-ink') }}>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-field bg-accent">
            <img src="{{ asset('images/title-marker-navy.png') }}" width="31" height="23" alt="" class="h-4 w-auto" aria-hidden="true">
        </span>
        {{ $slot }}
    </p>
@endif
