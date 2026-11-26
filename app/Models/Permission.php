<?php

namespace App\Models;

use App\Enums\RoleScope;
use App\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'public_id',
        'module',
        'name',
        'slug',
        'scope',
        'description',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scope' => RoleScope::class,
            'sort_order' => 'integer',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withTimestamps();
    }
}
