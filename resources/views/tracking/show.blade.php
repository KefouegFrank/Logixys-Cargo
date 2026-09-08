@php
    use App\Services\Documents\Barcode;
@endphp

@extends('layouts.public')

@section('title', $shipment->trackingNumber.' - '.__('tracking.title'))
@section('robots', 'noindex')

@section('content')
    <x-layout.container class="py-10 print:py-0">
        <div class="mx-auto max-w-3xl space-y-6">
            {{-- Tracking number, barcode, print --}}
            <div class="rounded-card border border-line bg-white p-6 text-center shadow-card print:border-0 print:p-0 print:shadow-none">
                <p class="text-sm text-ink-muted">{{ __('tracking.title') }}</p>
                <h1 class="font-heading text-2xl font-bold text-ink">{{ $shipment->trackingNumber }}</h1>

                <div class="mt-4 flex justify-center">
                    {!! Barcode::html($shipment->trackingNumber, 260, 46) !!}
                </div>

                <button
                    type="button"
                    onclick="window.print()"
                    class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-navy-700 underline decoration-2 underline-offset-4 transition-colors duration-200 hover:text-ink print:hidden"
                >
                    <x-heroicon-o-printer class="h-4 w-4" aria-hidden="true" />
                    {{ __('tracking.print_button') }}
                </button>
            </div>

            {{-- Progress: the four-step bar, or the exception banner for a status that broke out of it --}}
            <x-tracking.progress-bar :status="$shipment->status" />

            {{-- Parties: name is initials-only and no contact details are sent to this page at all --}}
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="rounded-card border border-line bg-white p-6 shadow-card">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-ink-subtle">{{ __('tracking.result_shipper') }}</h2>
                    <p class="mt-2 font-medium text-ink">{{ $shipment->shipperMasked }}</p>
                    <p class="text-sm text-ink-muted">{{ $shipment->shipperCity }}, {{ $shipment->shipperCountry }}</p>
                </div>
                <div class="rounded-card border border-line bg-white p-6 shadow-card">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-ink-subtle">{{ __('tracking.result_receiver') }}</h2>
                    <p class="mt-2 font-medium text-ink">{{ $shipment->receiverMasked }}</p>
                    <p class="text-sm text-ink-muted">{{ $shipment->receiverCity }}, {{ $shipment->receiverCountry }}</p>
                </div>
            </div>

            {{-- Shipment attributes --}}
            <div class="rounded-card border border-line bg-white p-6 shadow-card">
                <h2 class="text-xs font-bold uppercase tracking-wide text-ink-subtle">{{ __('tracking.info_heading') }}</h2>

                <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-4 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-ink-muted">{{ __('tracking.result_service_type') }}</dt>
                        <dd class="font-medium text-ink">{{ $shipment->serviceType->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-muted">{{ __('tracking.result_shipment_mode') }}</dt>
                        <dd class="font-medium text-ink">{{ $shipment->shipmentMode->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-muted">{{ __('tracking.result_origin') }}</dt>
                        <dd class="font-medium text-ink">{{ $shipment->shipperCity }}, {{ $shipment->shipperCountry }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-muted">{{ __('tracking.result_destination') }}</dt>
                        <dd class="font-medium text-ink">{{ $shipment->receiverCity }}, {{ $shipment->receiverCountry }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-muted">{{ __('tracking.result_packages') }}</dt>
                        <dd class="font-medium text-ink">{{ $shipment->packageCount }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-muted">{{ __('tracking.result_weight') }}</dt>
                        <dd class="font-medium text-ink">{{ number_format($shipment->totalWeightKg, 2, ',', ' ') }} kg</dd>
                    </div>
                    @if ($shipment->pickupDate)
                        <div>
                            <dt class="text-ink-muted">{{ __('tracking.result_pickup_date') }}</dt>
                            <dd class="font-medium text-ink">{{ $shipment->pickupDate->translatedFormat('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if ($shipment->expectedDeliveryDate)
                        <div>
                            <dt class="text-ink-muted">{{ __('tracking.result_expected_delivery') }}</dt>
                            <dd class="font-medium text-ink">{{ $shipment->expectedDeliveryDate->translatedFormat('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if ($shipment->goodsDescription)
                        <div class="col-span-2 sm:col-span-3">
                            <dt class="text-ink-muted">{{ __('tracking.result_goods') }}</dt>
                            <dd class="font-medium text-ink">{{ $shipment->goodsDescription }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Package lines: dimensions and weight only, no declared value --}}
            @if (count($shipment->packages))
                <div class="rounded-card border border-line bg-white p-6 shadow-card">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-ink-subtle">{{ __('tracking.packages_heading') }}</h2>
                    <div class="mt-4">
                        <x-tracking.packages-table :packages="$shipment->packages" />
                    </div>
                </div>
            @endif

            {{-- History: public events only, most recent first --}}
            @if (count($shipment->events))
                <div class="rounded-card border border-line bg-white p-6 shadow-card">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-ink-subtle">{{ __('tracking.timeline_heading') }}</h2>
                    <div class="mt-4">
                        <x-tracking.timeline :events="$shipment->events" />
                    </div>
                </div>
            @endif
        </div>
    </x-layout.container>
@endsection
