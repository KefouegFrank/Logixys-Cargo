@props(['events'])

@if (count($events))
    <div class="md:overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="hidden md:table-header-group">
                {{-- The reference's green header band, in the logo navy. --}}
                <tr class="bg-navy-900 text-xs font-semibold uppercase tracking-wide text-white">
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_date') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_time') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_location') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_status') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_updated_by') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.history_remarks') }}</th>
                </tr>
            </thead>
            <tbody class="block md:table-row-group md:divide-y md:divide-line">
                @foreach (collect($events)->reverse() as $event)
                    <tr class="mb-4 block rounded-card border border-line px-4 py-1 last:mb-0 md:mb-0 md:table-row md:rounded-none md:border-0 md:p-0 md:align-top md:transition-colors md:duration-200 md:hover:bg-navy-50">
                        <x-tracking.cell :label="__('tracking.history_date')" class="whitespace-nowrap text-ink-muted">{{ $event->occurred_at->translatedFormat('d/m/Y') }}</x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.history_time')" class="whitespace-nowrap text-ink-muted">{{ $event->occurred_at->format('H:i') }}</x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.history_location')" class="text-ink-muted">{{ $event->location_label ?: '—' }}</x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.history_status')" class="font-medium text-ink">{{ $event->status->label() }}</x-tracking.cell>
                        {{-- The company, as on the reference screen, not the agent who keyed it in. --}}
                        <x-tracking.cell :label="__('tracking.history_updated_by')" class="text-ink-muted">{{ config('app.name') }}</x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.history_remarks')" class="text-ink-muted">{{ $event->remarks ?: '—' }}</x-tracking.cell>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
