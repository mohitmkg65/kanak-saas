<?php

namespace App\Enums;

enum RegistrationSource: string
{
    case SelfRegistered = 'self_registered';
    case AdminCreated = 'admin_created';

    public function label(): string
    {
        return match ($this) {
            self::SelfRegistered => 'Self Registered',
            self::AdminCreated => 'Admin Created',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SelfRegistered => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            self::AdminCreated => 'bg-teal-100 text-teal-800 border-teal-200',
        };
    }
}
