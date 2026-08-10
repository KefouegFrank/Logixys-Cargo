<?php

namespace App\Filament\Resources\Shipments\Schemas;

use App\Enums\PaymentMode;
use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Services\ShipmentTotalsCalculator;
use App\Services\TrackingNumberGenerator;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service')
                ->columns(3)
                ->schema([
                    TextInput::make('tracking_number')
                        ->default(fn () => app(TrackingNumberGenerator::class)->generate())
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Generated automatically.'),
                    Select::make('status')
                        ->options(ShipmentStatus::class)
                        ->default(ShipmentStatus::Pending)
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Changed only from the status update action.'),
                    Select::make('service_type')
                        ->options(ServiceType::class)
                        ->required(),
                    Select::make('shipment_mode')
                        ->options(ShipmentMode::class)
                        ->required(),
                    TextInput::make('carrier_name'),
                    TextInput::make('carrier_reference'),
                    Select::make('locale')
                        ->options(['fr' => 'Français', 'en' => 'English'])
                        ->default('fr')
                        ->required(),
                    Hidden::make('created_by')
                        ->default(fn () => Auth::id()),
                ]),

            Section::make('Shipper')
                ->columns(2)
                ->schema([
                    TextInput::make('shipper_name')->required(),
                    TextInput::make('shipper_company'),
                    TextInput::make('shipper_email')->email(),
                    TextInput::make('shipper_phone')->tel(),
                    TextInput::make('shipper_address')->columnSpanFull(),
                    TextInput::make('shipper_postcode'),
                    TextInput::make('shipper_city')->required(),
                    TextInput::make('shipper_country')->maxLength(2)->default('FR')->required(),
                ]),

            Section::make('Receiver')
                ->columns(2)
                ->schema([
                    TextInput::make('receiver_name')->required(),
                    TextInput::make('receiver_company'),
                    TextInput::make('receiver_email')->email(),
                    TextInput::make('receiver_phone')->tel(),
                    TextInput::make('receiver_address')->columnSpanFull(),
                    TextInput::make('receiver_postcode'),
                    TextInput::make('receiver_city')->required(),
                    TextInput::make('receiver_country')->maxLength(2)->required(),
                ]),

            Section::make('Route')
                ->columns(3)
                ->schema([
                    TextInput::make('origin_label')->required()->columnSpanFull(),
                    TextInput::make('origin_lat')->numeric(),
                    TextInput::make('origin_lng')->numeric(),
                    TextInput::make('destination_label')->required()->columnSpanFull(),
                    TextInput::make('destination_lat')->numeric(),
                    TextInput::make('destination_lng')->numeric(),
                    TextInput::make('distance_km')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Computed once from the coordinates above when the shipment is created.'),
                ]),

            Section::make('Dates')
                ->columns(2)
                ->schema([
                    DatePicker::make('pickup_date'),
                    DatePicker::make('expected_delivery_date'),
                ]),

            Section::make('Goods')
                ->schema([
                    Textarea::make('goods_description')->columnSpanFull(),
                    TextInput::make('package_count')->numeric()->disabled()->dehydrated(false)->default(0),
                    TextInput::make('total_weight_kg')->numeric()->disabled()->dehydrated(false)->default(0),
                    TextInput::make('total_volume_cbm')->numeric()->disabled()->dehydrated(false)->default(0),
                    TextInput::make('declared_value')->numeric()->prefix('€'),
                    TextInput::make('currency')->maxLength(3)->default('EUR')->required(),
                ])
                ->columns(3)
                ->footer('Package count, weight, and volume are calculated from the package rows below.'),

            Section::make('Charges')
                ->columns(3)
                ->schema([
                    TextInput::make('freight_cost')->numeric()->prefix('€')->default(0)->live(onBlur: true)->afterStateUpdated(self::recalculateTotals(...)),
                    TextInput::make('insurance_cost')->numeric()->prefix('€')->default(0)->live(onBlur: true)->afterStateUpdated(self::recalculateTotals(...)),
                    TextInput::make('customs_cost')->numeric()->prefix('€')->default(0)->live(onBlur: true)->afterStateUpdated(self::recalculateTotals(...)),
                    TextInput::make('other_cost')->numeric()->prefix('€')->default(0)->live(onBlur: true)->afterStateUpdated(self::recalculateTotals(...)),
                    TextInput::make('tax_rate')->numeric()->suffix('%')->default(20)->live(onBlur: true)->afterStateUpdated(self::recalculateTotals(...)),
                    TextInput::make('tax_label')->default('TVA')->required(),
                    Textarea::make('tax_exemption_note')->columnSpanFull(),
                    Select::make('payment_mode')->options(PaymentMode::class),
                    TextInput::make('payment_status')->default('unpaid')->required(),
                    TextInput::make('total_ht')->numeric()->prefix('€')->disabled()->dehydrated(false),
                    TextInput::make('tax_amount')->numeric()->prefix('€')->disabled()->dehydrated(false),
                    TextInput::make('total_ttc')->numeric()->prefix('€')->disabled()->dehydrated(false),
                ]),
        ]);
    }

    private static function recalculateTotals(Get $get, Set $set): void
    {
        $totals = app(ShipmentTotalsCalculator::class)->calculate(
            (float) ($get('freight_cost') ?? 0),
            (float) ($get('insurance_cost') ?? 0),
            (float) ($get('customs_cost') ?? 0),
            (float) ($get('other_cost') ?? 0),
            (float) ($get('tax_rate') ?? 0),
        );

        $set('total_ht', $totals->totalHt);
        $set('tax_amount', $totals->taxAmount);
        $set('total_ttc', $totals->totalTtc);
    }
}
