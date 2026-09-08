@props(['packages', 'currency' => 'EUR'])

@php $dim = fn ($value) => $value === null ? null : rtrim(rtrim(number_format((float) $value, 1), '0'), '.'); @endphp

@if (count($packages))
    <div class="md:overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="hidden md:table-header-group">
                {{-- The reference's green header band, in the logo navy. --}}
                <tr class="bg-navy-900 text-xs font-semibold uppercase tracking-wide text-white">
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_quantity') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_type') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_description') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_dimensions') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_weight') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_unit_value') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_amount') }}</th>
                </tr>
            </thead>
            <tbody class="block md:table-row-group md:divide-y md:divide-line">
                @foreach ($packages as $package)
                    <tr class="mb-4 block rounded-card border border-line px-4 py-1 last:mb-0 md:mb-0 md:table-row md:rounded-none md:border-0 md:p-0 md:transition-colors md:duration-200 md:hover:bg-navy-50">
                        <x-tracking.cell :label="__('tracking.packages_quantity')" class="text-ink">{{ $package->quantity }}</x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.packages_type')" class="text-ink">{{ $package->package_type?->label() }}</x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.packages_description')" class="text-ink-muted">{{ $package->description ?: '—' }}</x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.packages_dimensions')" class="whitespace-nowrap text-ink-muted">
                            @if ($package->length_cm && $package->width_cm && $package->height_cm)
                                {{ $dim($package->length_cm) }} × {{ $dim($package->width_cm) }} × {{ $dim($package->height_cm) }}
                            @else
                                —
                            @endif
                        </x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.packages_weight')" class="text-ink-muted">
                            {{ $package->weight_kg !== null ? number_format((float) $package->weight_kg, 2, ',', ' ') : '—' }}
                        </x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.packages_unit_value')" class="whitespace-nowrap text-ink-muted">
                            {{ $package->unit_value !== null ? number_format((float) $package->unit_value, 2, ',', ' ').' '.$currency : '—' }}
                        </x-tracking.cell>
                        <x-tracking.cell :label="__('tracking.packages_amount')" class="whitespace-nowrap font-medium text-ink">
                            {{ $package->amount !== null ? number_format((float) $package->amount, 2, ',', ' ').' '.$currency : '—' }}
                        </x-tracking.cell>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
