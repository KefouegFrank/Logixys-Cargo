@php
    use App\Services\Documents\Barcode;

    $events = $shipment->events->where('is_public', true)->sortBy('occurred_at')->values();
    $currency = $shipment->currency ?: 'EUR';
    $money = fn ($amount) => number_format((float) $amount, 2, ',', ' ').' '.$currency;
    $hm = fn (?string $time) => $time ? substr($time, 0, 5) : null;
@endphp

@extends('layouts.public')

@section('title', $shipment->tracking_number.' - '.__('tracking.title'))
@section('robots', 'noindex')

@section('content')
    <x-layout.container class="py-10 print:py-0">
        <div class="mx-auto max-w-4xl space-y-8">
            {{-- Barcode and number, centred above everything else --}}
            <div class="text-center">
                <div class="mx-auto max-w-full overflow-x-auto">
                    <div class="flex w-max min-w-full justify-center">
                        {!! Barcode::html($shipment->tracking_number, 260, 56) !!}
                    </div>
                </div>
                <p class="mt-2 break-all font-heading text-base font-semibold tracking-wide text-ink sm:text-lg">{{ $shipment->tracking_number }}</p>

                <button
                    type="button"
                    onclick="window.print()"
                    class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-navy-700 underline decoration-2 underline-offset-4 transition-colors duration-200 hover:text-ink print:hidden"
                >
                    <x-heroicon-o-printer class="h-4 w-4" aria-hidden="true" />
                    {{ __('tracking.print_button') }}
                </button>
            </div>

            {{-- Parties --}}
            <div class="grid gap-8 sm:grid-cols-2">
                <x-tracking.party
                    :heading="__('tracking.result_shipper')"
                    :name="$shipment->shipper_name"
                    :company="$shipment->shipper_company"
                    :address="$shipment->shipper_address"
                    :locality="$shipment->partyLocality('shipper')"
                    :phone="$shipment->shipper_phone"
                    :email="$shipment->shipper_email"
                />
                <x-tracking.party
                    :heading="__('tracking.result_receiver')"
                    :name="$shipment->receiver_name"
                    :company="$shipment->receiver_company"
                    :address="$shipment->receiver_address"
                    :locality="$shipment->partyLocality('receiver')"
                    :phone="$shipment->receiver_phone"
                    :email="$shipment->receiver_email"
                />
            </div>

            {{-- Current state, the reference's grey band --}}
            <div class="text-balance bg-gray-500 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-white sm:text-sm">
                {{ __('tracking.state_banner') }} — {{ $shipment->status->label() }}
            </div>

            {{-- Progress: the four-step bar, or the exception banner for a status that broke out of it --}}
            <x-tracking.progress-bar :status="$shipment->status" />

            <x-tracking.section :heading="__('tracking.info_heading')">
                <dl class="grid grid-cols-1 gap-x-8 gap-y-5 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <x-tracking.field :label="__('tracking.result_origin')">{{ $shipment->origin_label }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_destination')">{{ $shipment->destination_label }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_status')">{{ $shipment->status->label() }}</x-tracking.field>

                    <x-tracking.field :label="__('tracking.result_service_type')">{{ $shipment->service_type?->label() }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_shipment_mode')">{{ $shipment->shipment_mode }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_carrier')">{{ $shipment->carrier_name ?: $shipment->carrier?->name }}</x-tracking.field>

                    <x-tracking.field :label="__('tracking.result_carrier_reference')">{{ $shipment->carrier_reference }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_packages')">{{ $shipment->package_count }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_quantity')">{{ $shipment->total_quantity }}</x-tracking.field>

                    <x-tracking.field :label="__('tracking.result_weight')">{{ number_format((float) $shipment->total_weight_kg, 2, ',', ' ') }} kg</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_volumetric_weight')">
                        @if ((float) $shipment->volumetric_weight_kg > 0){{ number_format((float) $shipment->volumetric_weight_kg, 2, ',', ' ') }} kg @endif
                    </x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_chargeable_weight')">
                        @if ((float) $shipment->chargeable_weight_kg > 0){{ number_format((float) $shipment->chargeable_weight_kg, 2, ',', ' ') }} kg @endif
                    </x-tracking.field>

                    <x-tracking.field :label="__('tracking.result_volume')">
                        @if ((float) $shipment->total_volume_cbm > 0){{ number_format((float) $shipment->total_volume_cbm, 3, ',', ' ') }} m³ @endif
                    </x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_distance')">
                        @if ($shipment->distance_km){{ number_format($shipment->distance_km, 0, ',', ' ') }} km @endif
                    </x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_pickup_date')">
                        {{ $shipment->pickup_date?->translatedFormat('d/m/Y') }}
                    </x-tracking.field>

                    <x-tracking.field :label="__('tracking.result_pickup_time')">{{ $hm($shipment->pickup_time) }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_departure_time')">{{ $hm($shipment->departure_time) }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_expected_delivery')">
                        {{ $shipment->expected_delivery_date?->translatedFormat('d/m/Y') }}
                    </x-tracking.field>

                    <x-tracking.field :label="__('tracking.result_delivered_at')">
                        {{ $shipment->delivered_at?->translatedFormat('d/m/Y H:i') }}
                    </x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_payment_mode')">{{ $shipment->payment_mode }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_payment_status')">{{ $shipment->paymentStatusLabel() }}</x-tracking.field>

                    <x-tracking.field :label="__('tracking.result_goods')" wide>{{ $shipment->goods_description }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.result_comments')" wide>{{ $events->last()?->remarks }}</x-tracking.field>
                </dl>
            </x-tracking.section>

            <x-tracking.map :shipment="$shipment" :events="$events" />

            <x-tracking.section :heading="__('tracking.charges_heading')">
                <dl class="grid grid-cols-1 gap-x-8 gap-y-5 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <x-tracking.field :label="__('tracking.charges_freight')">{{ $money($shipment->freight_cost) }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.charges_insurance')">{{ $money($shipment->insurance_cost) }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.charges_customs')">{{ $money($shipment->customs_cost) }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.charges_other')">{{ $money($shipment->other_cost) }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.charges_total_ht')">{{ $money($shipment->total_ht) }}</x-tracking.field>
                    <x-tracking.field :label="$shipment->tax_label ?: __('tracking.charges_tax')">
                        {{ $money($shipment->tax_amount) }} ({{ rtrim(rtrim(number_format((float) $shipment->tax_rate, 2, ',', ''), '0'), ',') }} %)
                    </x-tracking.field>
                    <x-tracking.field :label="__('tracking.charges_total_ttc')">{{ $money($shipment->total_ttc) }}</x-tracking.field>
                    <x-tracking.field :label="__('tracking.charges_declared_value')">
                        @if ((float) $shipment->declared_value > 0){{ $money($shipment->declared_value) }}@endif
                    </x-tracking.field>
                    <x-tracking.field :label="__('tracking.charges_exemption_note')" wide>{{ $shipment->tax_exemption_note }}</x-tracking.field>
                </dl>
            </x-tracking.section>

            @if ($shipment->packages->isNotEmpty())
                <x-tracking.section :heading="__('tracking.packages_heading')">
                    <x-tracking.packages-table :packages="$shipment->packages" :currency="$currency" />

                    <div class="mt-5 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                        <p>
                            <span class="font-semibold text-ink">{{ __('tracking.packages_total_volumetric') }} :</span>
                            <span class="text-ink-muted">{{ number_format((float) $shipment->volumetric_weight_kg, 2, ',', ' ') }} kg</span>
                        </p>
                        <p>
                            <span class="font-semibold text-ink">{{ __('tracking.packages_total_volume') }} :</span>
                            <span class="text-ink-muted">{{ number_format((float) $shipment->total_volume_cbm, 3, ',', ' ') }} m³</span>
                        </p>
                        <p>
                            <span class="font-semibold text-ink">{{ __('tracking.packages_total_weight') }} :</span>
                            <span class="text-ink-muted">{{ number_format((float) $shipment->total_weight_kg, 2, ',', ' ') }} kg</span>
                        </p>
                    </div>
                </x-tracking.section>
            @endif

            @if ($events->isNotEmpty())
                <x-tracking.section :heading="__('tracking.timeline_heading')">
                    <x-tracking.timeline :events="$events" />
                </x-tracking.section>
            @endif
        </div>
    </x-layout.container>
@endsection
