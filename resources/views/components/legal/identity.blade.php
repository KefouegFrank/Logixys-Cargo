@php
    $company = config('company');
    $contact = config('brand.contact');

    // Only real, known values are shown — nothing here is invented, and a field
    // with no value yet simply doesn't render a row rather than flagging a gap.
    $rows = collect([
        [__('legal.identity.legal_name'), $company['legal_name'] ?? config('app.name')],
        [__('legal.identity.legal_form'), $company['legal_form'] ?? null],
        [__('legal.identity.share_capital'), $company['share_capital'] ?? null],
        [__('legal.identity.address'), $company['address'] ?? ($contact['address'] ?? null)],
        [__('legal.identity.tax_id'), $company['tax_id'] ?? null],
        ...collect($company['identifiers'] ?? [])->map(fn ($i) => [$i['label'], $i['value']])->all(),
        [__('legal.identity.director'), $company['director'] ?? null],
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
