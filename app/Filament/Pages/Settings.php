<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

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
        $this->form->fill(config('brand.contact'));
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
            ]);
    }

    public function save(): void
    {
        Setting::putMany($this->form->getState());

        Notification::make()
            ->title('Coordonnées enregistrées')
            ->success()
            ->send();
    }
}
