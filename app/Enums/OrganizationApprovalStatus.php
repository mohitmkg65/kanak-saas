<?php

namespace App\Enums;

enum OrganizationApprovalStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::UnderReview => 'Under Review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Suspended => 'Suspended',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'badge-warning',
            self::UnderReview => 'badge-info',
            self::Approved => 'badge-success',
            self::Rejected => 'badge-danger',
            self::Suspended => 'badge-secondary',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::UnderReview => 'blue',
            self::Approved => 'green',
            self::Rejected => 'red',
            self::Suspended => 'gray',
        };
    }
}
