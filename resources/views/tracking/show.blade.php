@extends('layouts.public')

@section('title', $shipment->trackingNumber.' - '.__('tracking.title'))
@section('robots', 'noindex')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="font-heading text-2xl font-bold text-brand-navy">{{ $shipment->trackingNumber }}</h1>

        {{-- Four-step progress bar / exception banner: next chunk. --}}

        <dl class="mt-8 grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div>
                <dt class="text-gray-500">{{ __('tracking.result_service_type') }}</dt>
                <dd class="font-medium text-brand-navy">{{ $shipment->serviceType->label() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('tracking.result_shipment_mode') }}</dt>
                <dd class="font-medium text-brand-navy">{{ $shipment->shipmentMode->label() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('tracking.result_shipper') }}</dt>
                <dd class="font-medium text-brand-navy">{{ $shipment->shipperMasked }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('tracking.result_receiver') }}</dt>
                <dd class="font-medium text-brand-navy">{{ $shipment->receiverMasked }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('tracking.result_origin') }}</dt>
                <dd class="font-medium text-brand-navy">{{ $shipment->shipperCity }}, {{ $shipment->shipperCountry }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('tracking.result_destination') }}</dt>
                <dd class="font-medium text-brand-navy">{{ $shipment->receiverCity }}, {{ $shipment->receiverCountry }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('tracking.result_packages') }}</dt>
                <dd class="font-medium text-brand-navy">{{ $shipment->packageCount }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('tracking.result_weight') }}</dt>
                <dd class="font-medium text-brand-navy">{{ number_format($shipment->totalWeightKg, 2, ',', ' ') }} kg</dd>
            </div>
            @if ($shipment->pickupDate)
                <div>
                    <dt class="text-gray-500">{{ __('tracking.result_pickup_date') }}</dt>
                    <dd class="font-medium text-brand-navy">{{ $shipment->pickupDate->translatedFormat('d/m/Y') }}</dd>
                </div>
            @endif
            @if ($shipment->expectedDeliveryDate)
                <div>
                    <dt class="text-gray-500">{{ __('tracking.result_expected_delivery') }}</dt>
                    <dd class="font-medium text-brand-navy">{{ $shipment->expectedDeliveryDate->translatedFormat('d/m/Y') }}</dd>
                </div>
            @endif
            @if ($shipment->goodsDescription)
                <div class="col-span-2">
                    <dt class="text-gray-500">{{ __('tracking.result_goods') }}</dt>
                    <dd class="font-medium text-brand-navy">{{ $shipment->goodsDescription }}</dd>
                </div>
            @endif
        </dl>

        {{-- Public event timeline: next chunk. --}}

        {{-- Leaflet map: next chunk. --}}
    </div>
@endsection
