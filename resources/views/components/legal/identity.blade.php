@php
    $contact = config('brand.contact');

    // A field with no value yet is left out rather than flagged.
    $rows = collect([
        [__('legal.identity.legal_name'), config('app.name')],
        [__('legal.identity.address'), $contact['address'] ?? null],
        [__('legal.identity.email'), $contact['email'] ?? null],
        [__('legal.identity.phone'), $contact['phone'] ?? null],
    ])->filter(fn ($row) => filled($row[1]));
@endphp

<dl class="mt-4 divide-y divide-line overflow-hidden rounded-card border border-line text-sm">
    @foreach ($rows as [$label, $value])
        <div class="flex flex-col gap-1 px-5 py-3 sm:flex-row sm:items-baseline sm:gap-4">
            <dt class="w-56 shrink-0 font-semibold text-ink">{{ $label }}</dt>
            <dd class="text-ink-muted">{{ $value }}</dd>
        </div>
    @endforeach
</dl>
