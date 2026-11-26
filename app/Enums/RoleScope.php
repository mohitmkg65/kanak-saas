<?php

namespace App\Enums;

enum RoleScope: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case ORGANIZATION = 'ORGANIZATION';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ORGANIZATION => 'Organization',
        };
    }
}
