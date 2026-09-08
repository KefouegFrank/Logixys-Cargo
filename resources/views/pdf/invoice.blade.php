@php
    use App\Services\Documents\Barcode;

    $money = fn ($v) => number_format((float) $v, 2, ',', ' ').' '.$shipment->currency;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture {{ $receipt->number }}</title>
    @include('pdf.partials.styles')
</head>
<body>
    <table class="head">
        <tr>
            <td>
                <h1>LOGIXYS CARGO</h1>
                <div class="muted">{{ config('brand.contact.address') }}</div>
                <div class="muted">{{ config('brand.contact.email') }} · {{ config('brand.contact.phone') }}</div>
            </td>
            <td style="text-align: right;">
                <div style="font-size: 15px; font-weight: bold;">FACTURE {{ $receipt->number }}</div>
                <div class="muted">Émise le {{ $receipt->issued_at->format('d/m/Y') }}</div>
                <div style="margin-top: 6px;">{!! Barcode::html($shipment->tracking_number, 220, 38) !!}</div>
                <div class="muted" style="font-size: 9px;">{{ $shipment->tracking_number }}</div>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    @include('pdf.partials.parties')

    <table class="lines">
        <thead>
            <tr>
                <th>Qté.</th>
                <th>Type de pièce</th>
                <th>Description</th>
                <th class="num">Long. (cm)</th>
                <th class="num">Larg. (cm)</th>
                <th class="num">Haut. (cm)</th>
                <th class="num">Poids unit. (kg)</th>
                <th class="num">Valeur</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shipment->packages as $package)
                <tr>
                    <td>{{ $package->quantity }}</td>
                    <td>{{ $package->package_type->name }}</td>
                    <td>{{ $package->description }}</td>
                    <td class="num">{{ $package->length_cm }}</td>
                    <td class="num">{{ $package->width_cm }}</td>
                    <td class="num">{{ $package->height_cm }}</td>
                    <td class="num">{{ $package->weight_kg }}</td>
                    <td class="num">{{ $money($package->amount) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted">Aucun colis enregistré.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table style="margin-top: 10px;">
        <tr>
            <td style="width: 58%; vertical-align: top;">
                <div class="box">
                    <div class="label">Transport</div>
                    {{ $shipment->origin_label }} → {{ $shipment->destination_label }}<br>
                    <span class="muted">
                        {{ $shipment->service_type->label() }} · {{ $shipment->shipment_mode->label() }}
                        @if ($shipment->carrier_name) · {{ $shipment->carrier_name }} @endif
                    </span><br>
                    <span class="muted">
                        Poids réel {{ $shipment->total_weight_kg }} kg ·
                        volumétrique {{ $shipment->volumetric_weight_kg }} kg ·
                        taxable {{ $shipment->chargeable_weight_kg }} kg ·
                        volume {{ $shipment->total_volume_cbm }} m³
                    </span>
                </div>
            </td>
            <td>
                <table class="totals">
                    <tr><td>Fret</td><td class="num">{{ $money($shipment->freight_cost) }}</td></tr>
                    <tr><td>Assurance</td><td class="num">{{ $money($shipment->insurance_cost) }}</td></tr>
                    <tr><td>Douane</td><td class="num">{{ $money($shipment->customs_cost) }}</td></tr>
                    <tr><td>Autres frais</td><td class="num">{{ $money($shipment->other_cost) }}</td></tr>
                    <tr><td><strong>Total HT</strong></td><td class="num"><strong>{{ $money($receipt->total_ht) }}</strong></td></tr>
                    <tr><td>{{ $shipment->tax_label }} ({{ $shipment->tax_rate }} %)</td><td class="num">{{ $money($receipt->tax_amount) }}</td></tr>
                    <tr class="grand"><td>Total TTC</td><td class="num">{{ $money($receipt->total_ttc) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    @if ($shipment->tax_exemption_note)
        <div class="muted" style="clear: both; padding-top: 14px;">{{ $shipment->tax_exemption_note }}</div>
    @endif

    <div class="foot">
        {{ config('brand.contact.address') }} · {{ config('brand.contact.email') }} · Facture {{ $receipt->number }}
    </div>
</body>
</html>
