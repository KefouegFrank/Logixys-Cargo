@props(['events'])

@if (count($events))
    <ol class="mt-2 space-y-6 border-l-2 border-line pl-4">
        @foreach (array_reverse($events) as $event)
            <li class="relative">
                <span class="absolute -left-[1.4rem] top-1 h-2.5 w-2.5 rounded-full bg-ink"></span>
                <p class="font-medium text-ink">{{ $event->status->label() }}</p>
                @if ($event->locationLabel)
                    <p class="text-sm text-ink-muted">{{ $event->locationLabel }}</p>
                @endif
                <p class="text-sm text-ink-subtle">{{ $event->occurredAt->translatedFormat('d/m/Y H:i') }}</p>
            </li>
        @endforeach
    </ol>
@endif
