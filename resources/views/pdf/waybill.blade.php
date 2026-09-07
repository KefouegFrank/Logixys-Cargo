@php
    use App\Services\Documents\Barcode;

    // One page, three copies — accounts, consignee, shipper — as on the reference.
    $copies = ['Copie des comptes', 'Copie Consignee', 'Copie des expéditeurs'];
    $fmtDate = fn ($d) => $d?->format('d/m/Y') ?? '—';
    $fmtTime = fn ($t) => $t ? substr((string) $t, 0, 5) : '—';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Lettre de transport {{ $shipment->tracking_number }}</title>
    <style>
        @page { margin: 10mm 8mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8.5px; color: #102946; margin: 0; }
        .copy { border: 1px solid #102946; margin-bottom: 7mm; }
        table { width: 100%; border-collapse: collapse; }
        td { border: 1px solid #c7d6e8; padding: 4px 6px; vertical-align: top; }
        .code { text-align: center; width: 34%; }
        .code .num { font-weight: bold; font-size: 10px; margin-top: 3px; }
        .code .role { color: #466285; }
        .lbl { color: #6380a2; }
        .party { min-height: 46px; }
        .pkg th { background: #102946; color: #fff; font-size: 7.5px; text-transform: uppercase;
                  padding: 3px 5px; text-align: left; border: 1px solid #102946; }
        .pkg td { font-size: 8px; }
    </style>
</head>
<body>
@foreach ($copies as $copy)
    <div class="copy">
        <table>
            <tr>
                <td class="code" rowspan="3">
                    {!! Barcode::html($shipment->tracking_number, 240, 40) !!}
                    <div class="num">{{ $shipment->tracking_number }}</div>
                    <div class="role">{{ $copy }}</div>
                </td>
                <td><span class="lbl">Date de retrait :</span> {{ $fmtDate($shipment->pickup_date) }}</td>
                <td><span class="lbl">Heure de retrait :</span> {{ $fmtTime($shipment->pickup_time) }}</td>
                <td><span class="lbl">Date de livraison :</span> {{ $fmtDate($shipment->expected_delivery_date) }}</td>
            </tr>
            <tr>
                <td><span class="lbl">Origine :</span> {{ $shipment->origin_label }}</td>
                <td><span class="lbl">Destination :</span> {{ $shipment->destination_label }}</td>
                <td><span class="lbl">Courier :</span> {{ $shipment->carrier_name ?: '—' }}</td>
            </tr>
            <tr>
                <td><span class="lbl">Transporteur :</span> {{ $shipment->carrier?->name ?: ($shipment->carrier_name ?: '—') }}</td>
                <td><span class="lbl">N° de référence :</span> {{ $shipment->carrier_reference ?: '—' }}</td>
                <td><span class="lbl">Heure de départ :</span> {{ $fmtTime($shipment->departure_time) }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width:18%"><span class="lbl">Expéditeur</span></td>
                <td style="width:32%">{{ $shipment->shipper_name }}</td>
                <td style="width:18%"><span class="lbl">Destinataire</span></td>
                <td style="width:32%">{{ $shipment->receiver_name }}</td>
            </tr>
            <tr>
                <td class="party" colspan="2">
                    {{ $shipment->shipper_address }}<br>
                    {{ $shipment->shipper_city }}, {{ $shipment->shipper_country }}<br>
                    {{ $shipment->shipper_phone }}<br>
                    {{ $shipment->shipper_email }}
                </td>
                <td class="party" colspan="2">
                    {{ $shipment->receiver_address }}<br>
                    {{ $shipment->receiver_city }}, {{ $shipment->receiver_country }}<br>
                    {{ $shipment->receiver_phone }}<br>
                    {{ $shipment->receiver_email }}
                </td>
            </tr>
            <tr>
                <td><span class="lbl">Type d'expédition :</span><br>{{ $shipment->service_type->label() }}</td>
                <td><span class="lbl">Forfaits :</span> {{ $shipment->package_count }}</td>
                <td><span class="lbl">Produit :</span> {{ \Illuminate\Support\Str::limit($shipment->goods_description, 40) ?: '—' }}</td>
                <td><span class="lbl">Poids :</span> {{ $shipment->total_weight_kg }} kg</td>
            </tr>
            <tr>
                <td><span class="lbl">Total du fret :</span> {{ $shipment->freight_cost }} {{ $shipment->currency }}</td>
                <td><span class="lbl">Quantité :</span> {{ $shipment->total_quantity }}</td>
                <td><span class="lbl">Mode de paiement :</span> {{ $shipment->payment_mode?->label() ?: '—' }}</td>
                <td><span class="lbl">Mode :</span> {{ $shipment->shipment_mode->label() }}</td>
            </tr>
            <tr>
                <td><span class="lbl">Statut :</span> {{ $shipment->status->label() }}</td>
                <td colspan="3"><span class="lbl">Commentaire :</span> {{ $shipment->internal_notes }}</td>
            </tr>
        </table>
    </div>
@endforeach
</body>
</html>
