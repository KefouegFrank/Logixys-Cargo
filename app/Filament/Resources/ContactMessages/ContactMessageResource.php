<?php

namespace App\Filament\Resources\ContactMessages;

use App\Enums\ServiceType;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|\UnitEnum|null $navigationGroup = 'Exploitation';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $modelLabel = 'message';

    protected static ?string $pluralModelLabel = 'Messages';

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::query()->where('is_handled', false)->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Demande')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Nom'),
                    TextEntry::make('created_at')->label('Reçu le')->dateTime(),
                    TextEntry::make('email')->copyable(),
                    TextEntry::make('phone')->label('Téléphone')->placeholder('—'),
                    TextEntry::make('service_type')->label('Service')->badge()->placeholder('—'),
                    TextEntry::make('locale')->label('Langue'),
                    TextEntry::make('subject')->label('Sujet')->columnSpanFull()->placeholder('—'),
                    TextEntry::make('message')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_handled')
                    ->label('Traité')
                    ->boolean(),
                TextColumn::make('name')->label('Nom')->searchable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('service_type')->label('Service')->badge()->placeholder('—'),
                TextColumn::make('subject')->label('Sujet')->limit(40)->placeholder('—'),
                TextColumn::make('created_at')->label('Reçu le')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_handled')->label('Traité'),
                SelectFilter::make('service_type')->label('Service')->options(ServiceType::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
        ];
    }
}
