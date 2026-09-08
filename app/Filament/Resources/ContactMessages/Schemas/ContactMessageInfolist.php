<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Expéditeur')
                ->columns(3)
                ->schema([
                    TextEntry::make('name')->label('Nom'),
                    TextEntry::make('email')->label('E-mail')->copyable()->url(fn ($record) => 'mailto:'.$record->email),
                    TextEntry::make('phone')->label('Téléphone')->placeholder('—'),
                ]),
            Section::make('Demande')
                ->columns(3)
                ->schema([
                    TextEntry::make('subject')->label('Sujet')->badge(),
                    TextEntry::make('locale')->label('Langue')->formatStateUsing(fn (string $state) => strtoupper($state)),
                    TextEntry::make('created_at')->label('Reçu le')->dateTime('d/m/Y H:i'),
                    TextEntry::make('message')->label('Message')->columnSpanFull()->prose(),
                ]),
            Section::make('Traçabilité')
                ->columns(3)
                ->collapsed()
                ->schema([
                    TextEntry::make('ip_address')->label('Adresse IP')->placeholder('—'),
                    TextEntry::make('user_agent')->label('Navigateur')->placeholder('—')->columnSpan(2),
                    TextEntry::make('read_at')->label('Lu le')->dateTime('d/m/Y H:i')->placeholder('—'),
                    TextEntry::make('replied_at')->label('Répondu le')->dateTime('d/m/Y H:i')->placeholder('—'),
                ]),
        ]);
    }
}
