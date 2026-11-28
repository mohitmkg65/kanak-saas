<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'action',
        'from_status',
        'to_status',
        'from_approval_status',
        'to_approval_status',
        'reason',
        'action_by',
        'ip_address',
        'user_agent',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function actionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by');
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'registered' => 'Registration Submitted',
            'under_review' => 'Marked Under Review',
            'approved' => 'Approved',
            'reapproved' => 'Re-Approved',
            'rejected' => 'Rejected',
            'suspended' => 'Suspended',
            'reactivated' => 'Reactivated',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    public function actionBadgeClass(): string
    {
        return match ($this->action) {
            'registered' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'reapproved' => 'bg-teal-100 text-teal-800 border-teal-200',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
            'suspended' => 'bg-purple-100 text-purple-800 border-purple-200',
            'reactivated' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
