<?php

namespace App\Models;

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationStatus;
use App\Enums\OrganizationSubscriptionStatus;
use App\Enums\RegistrationSource;
use App\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Organization extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'public_id',
        'name',
        'legal_name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'logo',
        'status',
        'approval_status',
        'subscription_status',
        'registration_source',
        'created_by',
        'rejection_reason',
        'owner_email',
        'approved_at',
        'approved_by',
        'timezone',
        'currency',
        'locale',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrganizationStatus::class,
            'approval_status' => OrganizationApprovalStatus::class,
            'subscription_status' => OrganizationSubscriptionStatus::class,
            'registration_source' => RegistrationSource::class,
            'approved_at' => 'datetime',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, OrganizationMembership::class, 'organization_id', 'id', 'id', 'user_id');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function organizationProducts(): HasMany
    {
        return $this->hasMany(OrganizationProduct::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrganizationStatusLog::class)->latest();
    }

    public function logStatusChange(
        string $action,
        ?string $toStatus = null,
        ?string $toApprovalStatus = null,
        ?string $reason = null,
        ?int $actionBy = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): OrganizationStatusLog {
        $fromStatus = $this->status instanceof OrganizationStatus ? $this->status->value : $this->status;
        $fromApproval = $this->approval_status instanceof OrganizationApprovalStatus ? $this->approval_status->value : $this->approval_status;

        return $this->statusLogs()->create([
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus ?? $fromStatus,
            'from_approval_status' => $fromApproval,
            'to_approval_status' => $toApprovalStatus ?? $fromApproval,
            'reason' => $reason,
            'action_by' => $actionBy,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    public function isApproved(): bool
    {
        return $this->approval_status === OrganizationApprovalStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->approval_status === OrganizationApprovalStatus::Pending;
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', OrganizationApprovalStatus::Approved->value);
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', OrganizationApprovalStatus::Pending->value);
    }
}
