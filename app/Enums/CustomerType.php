<?php

namespace App\Enums;

enum CustomerType: string
{
    case Retail = 'retail';
    case Wholesale = 'wholesale';
    case Contractor = 'contractor';
    case Dealer = 'dealer';

    public function label(): string
    {
        return match ($this) {
            self::Retail => 'Retail',
            self::Wholesale => 'Wholesale',
            self::Contractor => 'Contractor',
            self::Dealer => 'Dealer',
        };
    }
}
