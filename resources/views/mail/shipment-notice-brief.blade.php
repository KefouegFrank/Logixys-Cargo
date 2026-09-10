<x-mail.layout :title="__('notifications.shipment_brief.kind.'.$kind)" :preheader="$shipment->tracking_number.' — '.$shipment->status->label()">
    <h1 style="margin:0 0 16px 0;font-size:20px;line-height:1.3;color:#102946;">
        {{ __('notifications.shipment_brief.kind.'.$kind) }}
    </h1>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <x-mail.row :label="__('notifications.shipment.tracking')" strong>{{ $shipment->tracking_number }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.status')" strong>{{ $shipment->status->label() }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.route')">{{ $shipment->origin_label }} &rarr; {{ $shipment->destination_label }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.location')">{{ $event?->location_label }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment.date')">{{ $event?->occurred_at?->translatedFormat('d/m/Y H:i') }}</x-mail.row>
        <x-mail.row :label="__('notifications.shipment_brief.notified')">
            {{ $notified !== [] ? implode(', ', $notified) : __('notifications.shipment_brief.nobody') }}
        </x-mail.row>
        <x-mail.row :label="__('notifications.shipment_brief.skipped')" color="#B3261E">
            {{ $skipped !== [] ? implode(', ', $skipped) : '' }}
        </x-mail.row>
    </table>

    <x-mail.button :url="$adminUrl">{{ __('notifications.shipment_brief.open') }}</x-mail.button>
</x-mail.layout>
