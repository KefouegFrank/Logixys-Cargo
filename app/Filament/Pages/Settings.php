<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\Geocoding\GeocodingService;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Paramètres';

    protected string $view = 'filament.pages.settings';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function getTitle(): string|Htmlable
    {
        return 'Coordonnées de l\'entreprise';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Ces valeurs alimentent le bandeau, le pied de page et la page contact du site public.';
    }

    public function mount(): void
    {
        $this->form->fill([...config('brand.contact'), 'company' => config('company')]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address')->label('Adresse')->columnSpanFull()->maxLength(255),
                        TextInput::make('email')->label('E-mail')->email()->maxLength(150),
                        TextInput::make('phone')->label('Téléphone (affiché)')->maxLength(40),
                        TextInput::make('phone_href')
                            ->label('Téléphone (lien tel:)')
                            ->helperText('Format international sans espaces, ex. +33123456789.')
                            ->maxLength(40),
                    ]),
                Section::make('Horaires')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hours_weekday')->label('Semaine')->maxLength(120),
                        TextInput::make('hours_weekend')->label('Week-end')->maxLength(120),
                    ]),
                Section::make('Médiateur de la consommation')
                    ->description('Requis si le site vend à des particuliers (Code de la consommation, art. L616-1) — l\'organisme de médiation auquel l\'entreprise a adhéré.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('company.mediator.name')->label('Nom de l\'organisme')->maxLength(150),
                        TextInput::make('company.mediator.url')->label('Site web')->url()->maxLength(255),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $company = Arr::pull($state, 'company', []);

        Setting::putMany([
            ...$state,
            ...$this->mapPosition($state['address'] ?? null),
            'company_mediator' => json_encode($company['mediator'] ?? []),
        ]);

        Notification::make()
            ->title('Coordonnées enregistrées')
            ->success()
            ->send();
    }

    // The contact-page pin follows the address, so the two can't drift apart.
    /** @return array{map_lat: ?string, map_lng: ?string} */
    private function mapPosition(?string $address): array
    {
        $coords = filled($address) ? app(GeocodingService::class)->geocode($address) : null;

        return [
            'map_lat' => isset($coords['lat']) ? (string) $coords['lat'] : null,
            'map_lng' => isset($coords['lng']) ? (string) $coords['lng'] : null,
        ];
    }
}
