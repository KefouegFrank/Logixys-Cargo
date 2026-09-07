<?php

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Models\Customer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Exploitation';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $modelLabel = 'client';

    protected static ?string $pluralModelLabel = 'Clients';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identité')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Nom')->required()->maxLength(150),
                    TextInput::make('company')->label('Société')->maxLength(150),
                    TextInput::make('email')->label('E-mail')->email()->maxLength(150),
                    TextInput::make('phone')->label('Téléphone')->tel()->maxLength(40),
                ]),
            Section::make('Adresse')
                ->columns(2)
                ->schema([
                    TextInput::make('address')->label('Adresse')->maxLength(255)->columnSpanFull(),
                    TextInput::make('postcode')->label('Code postal')->maxLength(20),
                    TextInput::make('city')->label('Ville')->maxLength(120),
                    TextInput::make('country')->label('Pays')->default('FR')->maxLength(2),
                ]),
            Section::make('Divers')
                ->schema([
                    Textarea::make('notes')->label('Notes')->rows(3)->columnSpanFull(),
                    Toggle::make('is_active')->label('Actif')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('company')->label('Société')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable()->copyable(),
                TextColumn::make('phone')->label('Téléphone'),
                TextColumn::make('city')->label('Ville')->searchable(),
                TextColumn::make('shipments_count')->label('Expéditions')->counts('shipments')->sortable(),
                IconColumn::make('is_active')->label('Actif')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Actif'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
        ];
    }
}
