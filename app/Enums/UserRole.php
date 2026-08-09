<?php

namespace App\Enums;

enum UserRole: string
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
}
