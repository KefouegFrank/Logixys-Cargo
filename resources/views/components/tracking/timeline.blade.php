@props(['events'])

@if (count($events))
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-line text-xs uppercase tracking-wide text-ink-subtle">
                    <th scope="col" class="py-2 pr-4 font-semibold">{{ __('tracking.history_date') }}</th>
                    <th scope="col" class="py-2 pr-4 font-semibold">{{ __('tracking.history_time') }}</th>
                    <th scope="col" class="py-2 pr-4 font-semibold">{{ __('tracking.history_status') }}</th>
                    <th scope="col" class="py-2 font-semibold">{{ __('tracking.history_location') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach (array_reverse($events) as $event)
                    <tr>
                        <td class="py-2.5 pr-4 text-ink-muted">{{ $event->occurredAt->translatedFormat('d/m/Y') }}</td>
                        <td class="py-2.5 pr-4 text-ink-muted">{{ $event->occurredAt->format('H:i') }}</td>
                        <td class="py-2.5 pr-4 font-medium text-ink">{{ $event->status->label() }}</td>
                        <td class="py-2.5 text-ink-muted">{{ $event->locationLabel ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
