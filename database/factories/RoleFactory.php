<?php

namespace Database\Factories;

use App\Enums\RoleScope;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $name = fake()->jobTitle();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'scope' => RoleScope::ORGANIZATION->value,
            'description' => fake()->sentence(),
            'is_system' => false,
            'status' => 'active',
            'organization_id' => null,
        ];
    }
}
