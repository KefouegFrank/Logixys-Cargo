<table class="grid">
    <tr>
        <td>
            <div class="box">
                <div class="label">Expéditeur</div>
                <strong>{{ $shipment->shipper_name }}</strong><br>
                @if ($shipment->shipper_address){{ $shipment->shipper_address }}<br>@endif
                @if ($shipment->partyLocality('shipper')){{ $shipment->partyLocality('shipper') }}<br>@endif
                <span class="muted">{{ $shipment->shipper_phone }} {{ $shipment->shipper_email }}</span>
            </div>
        </td>
        <td>
            <div class="box">
                <div class="label">Destinataire</div>
                <strong>{{ $shipment->receiver_name }}</strong><br>
                @if ($shipment->receiver_address){{ $shipment->receiver_address }}<br>@endif
                @if ($shipment->partyLocality('receiver')){{ $shipment->partyLocality('receiver') }}<br>@endif
                <span class="muted">{{ $shipment->receiver_phone }} {{ $shipment->receiver_email }}</span>
            </div>
        </td>
    </tr>
</table>
