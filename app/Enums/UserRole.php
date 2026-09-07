<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel
{
    case Admin = 'admin';
    case Agent = 'agent';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('Admin'),
            self::Agent => __('Agent'),
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
