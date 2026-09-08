<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Enums\ContactSubject;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('read_at')
                    ->label('Lu')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-s-envelope')
                    ->trueColor('gray')
                    ->falseColor('warning'),
                TextColumn::make('created_at')->label('Reçu le')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('email')->label('E-mail')->searchable()->copyable(),
                TextColumn::make('subject')->label('Sujet')->badge(),
                TextColumn::make('message')->label('Message')->limit(60)->wrap()->toggleable(),
                TextColumn::make('locale')->label('Langue')->badge()->color('gray')->formatStateUsing(fn (string $state) => strtoupper($state)),
            ])
            ->filters([
                SelectFilter::make('subject')->label('Sujet')->options(ContactSubject::class),
                Filter::make('unread')
                    ->label('Non lus uniquement')
                    ->query(fn (Builder $query) => $query->unread())
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucun message')
            ->emptyStateDescription('Les envois du formulaire de contact du site apparaissent ici.')
            ->recordUrl(fn (ContactMessage $record) => ContactMessageResource::getUrl('view', ['record' => $record]));
    }
}
