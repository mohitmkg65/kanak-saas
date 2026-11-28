<?php

namespace App\Models;

use App\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'public_id',
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'status',
        'created_by',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'category_unit');
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'category_brand');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
