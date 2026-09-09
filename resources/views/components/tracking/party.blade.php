{{-- Shipper or receiver block: everything the agent recorded for that party. --}}
@props([
    'heading', 'name',
    'company' => null, 'address' => null, 'locality' => null,
    'phone' => null, 'email' => null,
])

<x-tracking.section :heading="$heading">
    <div class="space-y-1 text-sm text-ink-muted">
        <p class="font-semibold text-ink">{{ $name }}</p>

        @if (filled($company))
            <p>{{ $company }}</p>
        @endif

        @if (filled($address))
            <p>{{ $address }}</p>
        @endif

        @if (filled($locality))
            <p>{{ $locality }}</p>
        @endif

        @if (filled($phone))
            <p>
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="transition-colors duration-200 hover:text-navy-700">{{ $phone }}</a>
            </p>
        @endif

        @if (filled($email))
            <p>
                <a href="mailto:{{ $email }}" class="break-all transition-colors duration-200 hover:text-navy-700">{{ $email }}</a>
            </p>
        @endif
    </div>
</x-tracking.section>
