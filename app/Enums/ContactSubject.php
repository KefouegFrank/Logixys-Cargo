<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContactSubject: string implements HasLabel
{
    case Quote = 'quote';
    case Tracking = 'tracking';
    case Claim = 'claim';
    case Partnership = 'partnership';
    case Other = 'other';

    public function label(): string
    {
        return __('contact.subjects.'.$this->value);
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
