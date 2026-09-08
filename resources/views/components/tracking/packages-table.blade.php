@props(['packages'])

@if (count($packages))
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-line text-xs uppercase tracking-wide text-ink-subtle">
                    <th scope="col" class="py-2 pr-4 font-semibold">{{ __('tracking.packages_quantity') }}</th>
                    <th scope="col" class="py-2 pr-4 font-semibold">{{ __('tracking.packages_type') }}</th>
                    <th scope="col" class="py-2 pr-4 font-semibold">{{ __('tracking.packages_description') }}</th>
                    <th scope="col" class="py-2 pr-4 font-semibold">{{ __('tracking.packages_dimensions') }}</th>
                    <th scope="col" class="py-2 font-semibold">{{ __('tracking.packages_weight') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach ($packages as $package)
                    <tr>
                        <td class="py-2.5 pr-4 text-ink">{{ $package->quantity }}</td>
                        <td class="py-2.5 pr-4 text-ink">{{ $package->packageType->label() }}</td>
                        <td class="py-2.5 pr-4 text-ink-muted">{{ $package->description ?? '—' }}</td>
                        <td class="py-2.5 pr-4 text-ink-muted">
                            @if ($package->lengthCm && $package->widthCm && $package->heightCm)
                                {{ rtrim(rtrim(number_format($package->lengthCm, 1), '0'), '.') }}
                                × {{ rtrim(rtrim(number_format($package->widthCm, 1), '0'), '.') }}
                                × {{ rtrim(rtrim(number_format($package->heightCm, 1), '0'), '.') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-2.5 text-ink-muted">
                            {{ $package->weightKg !== null ? number_format($package->weightKg, 2, ',', ' ') : '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
