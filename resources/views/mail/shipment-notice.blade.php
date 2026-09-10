@php
    // Delivered reads green, an exception red or amber, everything in flight navy.
    $accent = match (true) {
        $shipment->status === \App\Enums\ShipmentStatus::Delivered => '#0F7B4F',
        $shipment->status->exceptionSeverity() === 'danger' => '#B3261E',
        $shipment->status->exceptionSeverity() === 'warning' => '#8A5A00',
        default => '#102946',
    };
@endphp

<x-mail.layout :title="$headline" :preheader="$line">
    <h1 style="margin:0 0 4px 0;font-size:20px;line-height:1.3;color:#102946;">{{ $headline }}</h1>
    @if ($kind !== \App\Services\ShipmentNotifier::STATUS)
        {{-- On a status notice the headline already is the status; no need to say it twice. --}}
        <p style="margin:0 0 18px 0;font-size:14px;color:{{ $accent }};font-weight:bold;">{{ $shipment->status->label() }}</p>
    @endif

    <p style="margin:16px 0 12px 0;">{{ __('notifications.shipment.greeting', ['name' => $recipientName]) }}</p>
    <p style="margin:0 0 12px 0;">{{ __('notifications.shipment.intro_'.$party, ['tracking' => $shipment->tracking_number]) }}</p>
    <p style="margin:0 0 4px 0;">{{ $line }}</p>

    <div style="border-top:1px solid #C7D6E8;margin:22px 0 6px 0;"></div>
    <h2 style="margin:0 0 6px 0;font-size:16px;color:#102946;">{{ __('notifications.shipment.details_heading') }}</h2>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <x-mail.row :label="__('notifications.shipment.tracking')" strong>{{ $shipment->tracking_number }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.route')">{{ $shipment->origin_label }} &rarr; {{ $shipment->destination_label }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.status')" strong :color="$accent">{{ $shipment->status->label() }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.service')">{{ $shipment->service_type?->label() }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.carrier')">{{ $shipment->carrier_name }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.goods')">{{ $shipment->goods_description }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.packages')">
            @if ($shipment->package_count){{ $shipment->package_count }} &middot; {{ number_format((float) $shipment->total_weight_kg, 2, ',', ' ') }} kg @endif
        </x-mail.row>
        <x-mail.row :label="__('notifications.shipment.location')">{{ $event?->location_label }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.date')">
            {{ $event?->occurred_at?->translatedFormat('d/m/Y H:i') }}
        </x-mail.row>
        <x-mail.row :label="__('notifications.shipment.eta')">
            {{ $shipment->expected_delivery_date?->translatedFormat('d/m/Y') }}
        </x-mail.row>
    </table>

    @if ($event?->remarks)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:18px;">
            <tr>
                <td style="background-color:#F0F6FD;border-left:3px solid #F9D52A;padding:12px 14px;font-size:14px;line-height:1.55;color:#102946;">
                    {{ $event->remarks }}
                </td>
            </tr>
        </table>
    @endif

    <x-mail.button :url="$trackingUrl">{{ __('notifications.shipment.track') }}</x-mail.button>

    <p style="margin:18px 0 0 0;font-size:13px;color:#466285;">{{ __('notifications.shipment.support') }}</p>
    <p style="margin:14px 0 0 0;">{{ __('notifications.shipment.signoff') }}<br><strong>{{ config('app.name') }}</strong></p>
</x-mail.layout>
