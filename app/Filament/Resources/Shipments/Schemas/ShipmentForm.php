<?php

namespace App\Filament\Resources\Shipments\Schemas;

use App\Enums\LocationType;
use App\Enums\PackageType;
use App\Enums\PaymentMode;
use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Filament\Forms\Components\LocationPinField;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Services\Geocoding\GeocodingService;
use App\Services\PackageTotalsCalculator;
use App\Services\ShipmentTotalsCalculator;
use App\Services\TrackingNumberGenerator;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Auth;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    self::trackingSection(),
                    self::detailsSection(),
                ])->columnSpan(2),

                Group::make([
                    self::statusSection(),
                    self::mapSection(),
                    self::formActions(),
                ])->columnSpan(1),

                // Full width: the packages table carries eight columns and gets cramped
                // beside the sidebar.
                Group::make([
                    self::packagesSection(),
                    self::chargesSection(),
                ])->columnSpan(3),
            ]);
    }

    // The number sits on its own above everything else, the way the reference screen
    // puts it in the title box.
    private static function trackingSection(): Section
    {
        return Section::make()
            ->columns(1)
            ->schema([
                TextInput::make('tracking_number')
                    ->hiddenLabel()
                    ->default(fn () => app(TrackingNumberGenerator::class)->generate())
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    // Dehydrated so the number shown on screen is the one that gets saved,
                    // rather than a preview the model would replace at insert time.
                    ->dehydrated()
                    // Inline: the panel has no custom Tailwind build, so utility classes here would no-op.
                    ->extraInputAttributes(['style' => 'font-size:1.125rem;font-weight:600;letter-spacing:0.02em;']),
                Hidden::make('created_by')->default(fn () => Auth::id()),

                // Filled from the Origine / Destination selects below; the shipment keeps
                // its own copy so later edits to a location never rewrite past bookings.
                Hidden::make('origin_label'),
                Hidden::make('origin_lat'),
                Hidden::make('origin_lng'),
                Hidden::make('destination_label'),
                Hidden::make('destination_lat'),
                Hidden::make('destination_lng'),
            ]);
    }

    /**
     * Posting a tracking event. Leaving Statut empty saves the shipment without touching
     * the history, which is how the reference screen behaves.
     */
    private static function statusSection(): Section
    {
        return Section::make('Statut actuel')
            ->columns(1)
            ->schema([
                Text::make(fn (?Shipment $record) => 'Statut : '.($record?->status?->label() ?? ShipmentStatus::Pending->label()))
                    ->weight(FontWeight::Bold),
                DatePicker::make('event_date')
                    ->label('Date')
                    ->default(fn () => now()->toDateString()),
                TimePicker::make('event_time')
                    ->label('Heure')
                    ->seconds(false)
                    ->default(fn () => now()->format('H:i')),
                TextInput::make('event_location')
                    ->label('Emplacement')
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        if (blank($state)) {
                            return;
                        }

                        $coords = app(GeocodingService::class)->geocode($state);

                        if ($coords !== null) {
                            $set('event_position', ['lat' => $coords['lat'], 'lng' => $coords['lng'], 'isManual' => false]);
                        }
                    })
                    ->datalist(fn () => ShipmentEvent::query()
                        ->whereNotNull('location_label')
                        ->distinct()
                        ->orderBy('location_label')
                        ->limit(50)
                        ->pluck('location_label')
                        ->all()),
                Select::make('event_status')
                    ->label('Statut')
                    ->options(ShipmentStatus::class)
                    ->placeholder('Sélectionner le type'),
                Textarea::make('event_remarks')
                    ->label('Remarques')
                    ->rows(3),
                Select::make('locale')
                    ->label('Langue du client')
                    ->options(fn () => collect(config('locales'))->map(fn (array $l) => $l['native'])->all())
                    ->default(config('app.locale'))
                    ->required()
                    ->helperText('Langue des e-mails et documents destinés au client.'),
            ]);
    }

    // Sits under the status box so the pin can be corrected next to the location it came from.
    private static function mapSection(): Section
    {
        return Section::make('Position')
            ->columns(1)
            ->schema([
                LocationPinField::make('event_position')
                    ->hiddenLabel()
                    ->popupFrom('event_location', 'event_status', collect(ShipmentStatus::cases())
                        ->mapWithKeys(fn (ShipmentStatus $status) => [$status->value => $status->label()])
                        ->all()),
            ]);
    }

    // Create / Save / Cancel, rendered under the sidebar instead of the page footer.
    private static function formActions(): Actions
    {
        return Actions::make(fn ($livewire) => $livewire->getSidebarFormActions())
            ->fullWidth();
    }

    private static function detailsSection(): Section
    {
        return Section::make('Détails de l\'envoi')
            ->columns(1)
            ->inlineLabel()
            ->schema([
                Grid::make(2)->schema([
                    Group::make()->schema([
                        self::heading('DÉTAILS DE L\'EXPÉDITEUR'),
                        ...self::partyFields('shipper', 'Nom de l\'expéditeur'),
                    ]),
                    Group::make()->schema([
                        self::heading('DÉTAILS DU DESTINATAIRE'),
                        ...self::partyFields('receiver', 'Nom du destinataire'),
                    ]),
                ]),

                self::heading('DÉTAILS DE L\'ENVOI'),

                Grid::make(2)->schema([
                    Group::make()->schema(self::consignmentLeftColumn()),
                    Group::make()->schema(self::consignmentRightColumn()),
                ]),

            ]);
    }

    private static function heading(string $text): Text
    {
        return Text::make($text)
            ->size(TextSize::Large)
            ->weight(FontWeight::Bold)
            ->color('primary')
            ->columnSpanFull();
    }

    /** @return array<int, mixed> */
    private static function partyFields(string $prefix, string $nameLabel): array
    {
        return [
            TextInput::make("{$prefix}_name")->label($nameLabel)->required()->maxLength(150),
            TextInput::make("{$prefix}_phone")->label('Numéro de téléphone')->tel()->maxLength(40),
            TextInput::make("{$prefix}_email")->label('E-mail')->email()->maxLength(150),
            TextInput::make("{$prefix}_address")->label('Adresse')->maxLength(255),
            TextInput::make("{$prefix}_city")->label('Ville')->required()->maxLength(120),
            TextInput::make("{$prefix}_country")
                ->label('Pays')
                ->default($prefix === 'shipper' ? 'FR' : null)
                ->required()
                ->maxLength(2),
        ];
    }

    /** @return array<int, mixed> */
    private static function consignmentLeftColumn(): array
    {
        return [
            Select::make('service_type')
                ->label('Type d\'expédition')->options(ServiceType::class)->required(),
            self::derived('total_weight_kg', 'Poids', 'kg'),
            self::derived('package_count', 'Forfaits'),
            Textarea::make('goods_description')->label('Produit')->rows(2),
            Select::make('payment_mode')
                ->label('Mode de paiement')->options(PaymentMode::class),
            Select::make('carrier_id')
                ->label('Transporteur')
                ->relationship('carrier', 'name')
                ->searchable()
                ->preload()
                ->live()
                // carrier_name is the label kept on the shipment; the FK only records which
                // carrier record it came from.
                ->afterStateUpdated(fn (Set $set, $state) => $set('carrier_name', $state ? Carrier::find($state)?->name : null))
                ->createOptionForm([
                    TextInput::make('name')->label('Nom')->required()->maxLength(120),
                    TextInput::make('code')->label('Code')->maxLength(20),
                    TextInput::make('contact_email')->label('E-mail')->email()->maxLength(150),
                    TextInput::make('contact_phone')->label('Téléphone')->tel()->maxLength(40),
                ]),
            TimePicker::make('departure_time')->label('Heure de départ')->seconds(false),
            self::locationSelect('destination', 'Destination'),
            TimePicker::make('pickup_time')->label('Heure de ramassage')->seconds(false),
        ];
    }

    /** @return array<int, mixed> */
    private static function consignmentRightColumn(): array
    {
        return [
            TextInput::make('carrier_name')
                ->label('Courier')
                ->maxLength(120)
                ->helperText('Repris du transporteur choisi'),
            Select::make('shipment_mode')
                ->label('Mode')->options(ShipmentMode::class)->required(),
            self::derived('total_quantity', 'Quantité'),
            TextInput::make('freight_cost')
                ->label('Total du fret')
                ->numeric()
                ->prefix('€')
                ->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(self::recalculateTotals(...)),
            TextInput::make('carrier_reference')->label('Numéro de référence du transporteur')->maxLength(120),
            self::locationSelect('origin', 'Origine'),
            DatePicker::make('pickup_date')->label('Date de retrait'),
            DatePicker::make('expected_delivery_date')->label('Date de livraison souhaitée'),
        ];
    }

    /**
     * Origine and Destination are managed lists here, as in the reference. Picking one
     * copies its label and coordinates onto the shipment, which then owns them — editing
     * the location later never rewrites past bookings.
     */
    private static function locationSelect(string $prefix, string $label): Select
    {
        $relationship = $prefix === 'origin' ? 'originLocation' : 'destinationLocation';

        return Select::make("{$prefix}_location_id")
            ->label($label)
            ->relationship($relationship, 'name')
            ->getOptionLabelFromRecordUsing(fn (Location $record) => $record->label())
            ->searchable(['name', 'city'])
            ->preload()
            ->required()
            ->live()
            ->afterStateUpdated(function (Set $set, $state) use ($prefix) {
                $location = $state ? Location::find($state) : null;

                if ($location === null) {
                    return;
                }

                $set("{$prefix}_label", $location->label());
                $set("{$prefix}_lat", $location->lat);
                $set("{$prefix}_lng", $location->lng);
            })
            ->createOptionForm([
                TextInput::make('name')->label('Nom')->required()->maxLength(150),
                Select::make('type')->label('Type')->options(LocationType::class)->default(LocationType::City)->required(),
                TextInput::make('city')->label('Ville')->maxLength(120),
                TextInput::make('country')->label('Pays')->default('FR')->maxLength(2),
                TextInput::make('lat')->label('Latitude')->numeric(),
                TextInput::make('lng')->label('Longitude')->numeric(),
            ]);
    }

    /**
     * Rolled up from the package rows by syncDerived(), then editable — an agent can
     * override any of them, and what is on screen is what gets saved.
     */
    private static function derived(string $name, string $label, ?string $suffix = null): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->suffix($suffix)
            ->numeric()
            ->default(0);
    }

    // Recomputed whenever a package row changes, and on load for an existing shipment.
    private static function syncDerived(Get $get, Set $set): void
    {
        $totals = app(PackageTotalsCalculator::class)->calculate($get('packages') ?? []);

        $set('total_weight_kg', number_format($totals->actualWeightKg, 2, '.', ''));
        $set('package_count', $totals->count);
        $set('total_quantity', $totals->quantity);
        $set('volumetric_weight_kg', number_format($totals->volumetricWeightKg, 2, '.', ''));
        $set('chargeable_weight_kg', number_format($totals->chargeableWeightKg, 2, '.', ''));
        $set('total_volume_cbm', number_format($totals->volumeCbm, 3, '.', ''));
        $set('declared_value', number_format($totals->declaredValue, 2, '.', ''));
    }

    private static function packagesSection(): Section
    {
        return Section::make('Forfaits')
            ->description('Une ligne par type de colis. Les totaux se recalculent à la saisie.')
            ->schema([
                Repeater::make('packages')
                    ->hiddenLabel()
                    ->relationship()
                    ->live()
                    ->afterStateUpdated(self::syncDerived(...))
                    ->table([
                        TableColumn::make('Qté.')->markAsRequired(),
                        TableColumn::make('Type de pièce')->markAsRequired(),
                        TableColumn::make('Description'),
                        TableColumn::make('Long. (cm)'),
                        TableColumn::make('Larg. (cm)'),
                        TableColumn::make('Haut. (cm)'),
                        TableColumn::make('Poids (kg)'),
                        TableColumn::make('Valeur unit. (€)'),
                    ])
                    ->schema([
                        TextInput::make('quantity')->numeric()->minValue(1)->default(1)->required()->live(onBlur: true),
                        Select::make('package_type')->label('Type de pièce')->options(PackageType::class)->required(),
                        TextInput::make('description')->maxLength(255),
                        TextInput::make('length_cm')->numeric()->live(onBlur: true),
                        TextInput::make('width_cm')->numeric()->live(onBlur: true),
                        TextInput::make('height_cm')->numeric()->live(onBlur: true),
                        TextInput::make('weight_kg')->numeric()->live(onBlur: true),
                        TextInput::make('unit_value')->numeric()->live(onBlur: true),
                    ])
                    ->addActionLabel('Ajouter un Package')
                    ->defaultItems(1)
                    ->reorderable(false)
                    ->columnSpanFull(),

                Grid::make(3)->schema([
                    self::derived('volumetric_weight_kg', 'Poids volumétrique total', 'kg'),
                    self::derived('chargeable_weight_kg', 'Poids taxable', 'kg'),
                    self::derived('total_volume_cbm', 'Volume total', 'm³'),
                    self::derived('declared_value', 'Valeur déclarée', '€'),
                ]),
            ]);
    }

    private static function chargesSection(): Section
    {
        return Section::make('Tarification')
            ->columns(3)
            ->schema([
                TextInput::make('insurance_cost')->label('Assurance')->numeric()->prefix('€')->default(0)->live(onBlur: true)->afterStateUpdated(self::recalculateTotals(...)),
                TextInput::make('customs_cost')->label('Douane')->numeric()->prefix('€')->default(0)->live(onBlur: true)->afterStateUpdated(self::recalculateTotals(...)),
                TextInput::make('other_cost')->label('Autres frais')->numeric()->prefix('€')->default(0)->live(onBlur: true)->afterStateUpdated(self::recalculateTotals(...)),
                TextInput::make('tax_rate')->label('Taux de taxe')->numeric()->suffix('%')->default(20)->live(onBlur: true)->afterStateUpdated(self::recalculateTotals(...)),
                TextInput::make('tax_label')->label('Libellé de taxe')->default('TVA')->required()->maxLength(32),
                TextInput::make('payment_status')->label('Statut de paiement')->default('unpaid')->required()->maxLength(16),
                TextInput::make('currency')->label('Devise')->default('EUR')->required()->maxLength(3),
                Textarea::make('tax_exemption_note')->label('Note d\'exonération')->rows(2)->columnSpanFull(),
                TextInput::make('total_ht')->label('Total HT')->numeric()->prefix('€'),
                TextInput::make('tax_amount')->label('Montant de la taxe')->numeric()->prefix('€'),
                TextInput::make('total_ttc')->label('Total TTC')->numeric()->prefix('€'),
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
