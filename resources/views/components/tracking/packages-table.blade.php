@props(['packages', 'currency' => 'EUR'])

@php $dim = fn ($value) => $value === null ? null : rtrim(rtrim(number_format((float) $value, 1), '0'), '.'); @endphp

@if (count($packages))
    {{-- min-w keeps the columns readable and lets the wrapper scroll on a phone, rather
         than letting seven columns squash into an unreadable grid. --}}
    <div class="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
        <table class="w-full min-w-[46rem] text-left text-sm">
            <thead>
                {{-- The reference's green header band, in the logo navy. --}}
                <tr class="bg-navy-900 text-xs font-semibold uppercase tracking-wide text-white">
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_quantity') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_type') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_description') }}</th>
                    <th scope="col" class="whitespace-nowrap px-3 py-2.5">{{ __('tracking.packages_dimensions') }}</th>
                    <th scope="col" class="whitespace-nowrap px-3 py-2.5">{{ __('tracking.packages_weight') }}</th>
                    <th scope="col" class="whitespace-nowrap px-3 py-2.5">{{ __('tracking.packages_unit_value') }}</th>
                    <th scope="col" class="px-3 py-2.5">{{ __('tracking.packages_amount') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach ($packages as $package)
                    <tr class="transition-colors duration-200 hover:bg-navy-50">
                        <td class="px-3 py-2.5 text-ink">{{ $package->quantity }}</td>
                        <td class="px-3 py-2.5 text-ink">{{ $package->package_type?->label() }}</td>
                        <td class="px-3 py-2.5 text-ink-muted">{{ $package->description ?: '—' }}</td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-ink-muted">
                            @if ($package->length_cm && $package->width_cm && $package->height_cm)
                                {{ $dim($package->length_cm) }} × {{ $dim($package->width_cm) }} × {{ $dim($package->height_cm) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-ink-muted">
                            {{ $package->weight_kg !== null ? number_format((float) $package->weight_kg, 2, ',', ' ') : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-ink-muted">
                            {{ $package->unit_value !== null ? number_format((float) $package->unit_value, 2, ',', ' ').' '.$currency : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-2.5 font-medium text-ink">
                            {{ $package->amount !== null ? number_format((float) $package->amount, 2, ',', ' ').' '.$currency : '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
