<?php

namespace App\Models;

use App\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationProduct extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'public_id',
        'organization_id',
        'product_id',
        'name',
        'sku',
        'category_id',
        'unit_id',
        'hsn_code',
        'brand',
        'description',
        'selling_price',
        'purchase_price',
        'mrp',
        'opening_stock',
        'min_stock_level',
        'tax_rate',
        'is_active',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'mrp' => 'decimal:2',
            'opening_stock' => 'decimal:2',
            'min_stock_level' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * The global catalog product this org product is linked to (null for custom products).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Whether this is a custom org product (not linked to global catalog).
     */
    public function isCustom(): bool
    {
        return $this->product_id === null;
    }

    /**
     * Whether this is linked to a global catalog product.
     */
    public function isFromCatalog(): bool
    {
        return $this->product_id !== null;
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCustom($query)
    {
        return $query->whereNull('product_id');
    }

    public function scopeFromCatalog($query)
    {
        return $query->whereNotNull('product_id');
    }
}
