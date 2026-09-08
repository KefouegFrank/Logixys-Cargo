@props(['events'])

@if (count($events))
    <div class="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
        <table class="w-full min-w-[46rem] text-left text-sm">
            <thead>
                {{-- The reference's green header band, in the logo navy. --}}
                <tr class="bg-navy-900 text-xs font-semibold uppercase tracking-wide text-white">
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_date') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_time') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_location') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_status') }}</th>
                    <th scope="col" class="whitespace-nowrap px-3 py-2.5">{{ __('tracking.history_updated_by') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_remarks') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach (collect($events)->reverse() as $event)
                    <tr class="align-top transition-colors duration-200 hover:bg-navy-50">
                        <td class="whitespace-nowrap px-3 py-2.5 text-ink-muted">{{ $event->occurred_at->translatedFormat('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-ink-muted">{{ $event->occurred_at->format('H:i') }}</td>
                        <td class="px-3 py-2.5 text-ink-muted">{{ $event->location_label ?: '—' }}</td>
                        <td class="px-3 py-2.5 font-medium text-ink">{{ $event->status->label() }}</td>
                        {{-- The company, as on the reference screen, not the agent who keyed it in. --}}
                        <td class="whitespace-nowrap px-3 py-2.5 text-ink-muted">{{ config('app.name') }}</td>
                        <td class="px-3 py-2.5 text-ink-muted">{{ $event->remarks ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
