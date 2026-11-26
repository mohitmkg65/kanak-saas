<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'public_id',
        'organization_id',
        'name',
        'email',
        'phone',
        'company_name',
        'gstin',
        'customer_type',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_postal_code',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_postal_code',
        'credit_limit',
        'outstanding_balance',
        'notes',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'customer_type' => CustomerType::class,
            'status' => CustomerStatus::class,
            'credit_limit' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', CustomerStatus::Active->value);
    }
}
