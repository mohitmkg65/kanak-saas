<?php

namespace App\Enums;

enum OrganizationSubscriptionStatus: string
{
    case Trialing = 'trialing';
    case Active = 'active';
    case PastDue = 'past_due';
    case Canceled = 'canceled';
    case Unpaid = 'unpaid';

    public function label(): string
    {
        return match ($this) {
            self::Trialing => 'Trialing',
            self::Active => 'Active',
            self::PastDue => 'Past Due',
            self::Canceled => 'Canceled',
            self::Unpaid => 'Unpaid',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Active => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::Trialing => 'bg-cyan-100 text-cyan-800 border-cyan-300',
            self::PastDue => 'bg-amber-100 text-amber-800 border-amber-300',
            self::Canceled => 'bg-slate-100 text-slate-800 border-slate-300',
            self::Unpaid => 'bg-rose-100 text-rose-800 border-rose-300',
        };
    }
}
