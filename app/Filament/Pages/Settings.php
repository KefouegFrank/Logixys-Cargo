<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
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
                Section::make('Position sur la carte')
                    ->description('Le point affiché sur la page contact.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('map_lat')->label('Latitude')->numeric()->minValue(-90)->maxValue(90),
                        TextInput::make('map_lng')->label('Longitude')->numeric()->minValue(-180)->maxValue(180),
                    ]),
                Section::make('Horaires')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hours_weekday')->label('Semaine')->maxLength(120),
                        TextInput::make('hours_weekend')->label('Week-end')->maxLength(120),
                    ]),
                Section::make('Identité légale')
                    ->description('Alimente les mentions légales, la politique de confidentialité et les CGV. Tant que ces champs sont vides, ces pages affichent un bandeau "à compléter" au public.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('company.legal_name')->label('Raison sociale')->maxLength(150),
                        TextInput::make('company.legal_form')->label('Forme juridique')->placeholder('ex. SAS, SARL, EI')->maxLength(60),
                        TextInput::make('company.share_capital')->label('Capital social')->placeholder('ex. 10 000 €')->maxLength(40),
                        TextInput::make('company.tax_id')->label('N° TVA intracommunautaire')->maxLength(30),
                        TextInput::make('company.address')
                            ->label('Adresse du siège social')
                            ->helperText('Celle des mentions légales — peut différer de l\'adresse publique ci-dessus.')
                            ->columnSpanFull()
                            ->maxLength(255),
                        TextInput::make('company.director')->label('Directeur de la publication')->columnSpanFull()->maxLength(150),
                        Repeater::make('company.identifiers')
                            ->label('Numéros d\'immatriculation')
                            ->helperText('SIRET, RCS, etc. — un couple libellé / valeur par ligne.')
                            ->table([
                                TableColumn::make('Libellé'),
                                TableColumn::make('Valeur'),
                            ])
                            ->schema([
                                TextInput::make('label')->placeholder('ex. SIRET')->required(),
                                TextInput::make('value')->placeholder('ex. 123 456 789 00012')->required(),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Ajouter un numéro')
                            ->reorderable(false),
                    ]),
                Section::make('Hébergeur')
                    ->description('Obligatoire dans les mentions légales (LCEN art. 6-III) — l\'entité juridique de l\'hébergeur, pas seulement sa marque.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('company.host.name')->label('Raison sociale')->maxLength(150),
                        TextInput::make('company.host.phone')->label('Téléphone')->maxLength(40),
                        TextInput::make('company.host.address')->label('Adresse')->columnSpanFull()->maxLength(255),
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
            'company_legal_name' => $company['legal_name'] ?? null,
            'company_legal_form' => $company['legal_form'] ?? null,
            'company_share_capital' => $company['share_capital'] ?? null,
            'company_address' => $company['address'] ?? null,
            'company_tax_id' => $company['tax_id'] ?? null,
            'company_director' => $company['director'] ?? null,
            'company_identifiers' => json_encode(array_values($company['identifiers'] ?? [])),
            'company_host' => json_encode($company['host'] ?? []),
            'company_mediator' => json_encode($company['mediator'] ?? []),
        ]);

        Notification::make()
            ->title('Coordonnées enregistrées')
            ->success()
            ->send();
    }
}
