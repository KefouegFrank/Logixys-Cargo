<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function getTitle(): string
    {
        return 'Message de '.$this->record->name;
    }

    // Opening it is what marks it read; nobody has to remember to tick a box.
    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->markAsRead();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Répondre par e-mail')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('primary')
                ->url(fn (ContactMessage $record) => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: '.$record->subject->label()))
                ->openUrlInNewTab()
                ->after(fn (ContactMessage $record) => $record->forceFill(['replied_at' => now()])->save()),
            Action::make('markUnread')
                ->label('Marquer comme non lu')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->visible(fn (ContactMessage $record) => $record->isRead())
                ->action(function (ContactMessage $record) {
                    $record->forceFill(['read_at' => null])->save();

                    Notification::make()->title('Marqué comme non lu')->success()->send();

                    $this->redirect(ContactMessageResource::getUrl('index'));
                }),
            DeleteAction::make(),
        ];
    }
}
