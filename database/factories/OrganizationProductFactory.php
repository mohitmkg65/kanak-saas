<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Organization;
use App\Models\OrganizationProduct;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizationProduct>
 */
class OrganizationProductFactory extends Factory
{
    protected $model = OrganizationProduct::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'product_id' => null,
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('ORG-???-####')),
            'category_id' => Category::factory(),
            'unit_id' => Unit::factory(),
            'hsn_code' => fake()->optional()->numerify('######'),
            'brand' => fake()->optional()->company(),
            'description' => fake()->optional()->sentence(),
            'selling_price' => fake()->randomFloat(2, 10, 50000),
            'purchase_price' => fake()->randomFloat(2, 5, 40000),
            'mrp' => fake()->optional()->randomFloat(2, 15, 55000),
            'opening_stock' => fake()->randomFloat(2, 0, 1000),
            'min_stock_level' => fake()->optional()->randomFloat(2, 1, 100),
            'tax_rate' => fake()->randomElement([5.00, 12.00, 18.00, 28.00]),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }

    /**
     * Create an org product linked to a global catalog product.
     */
    public function fromCatalog(?Product $product = null): static
    {
        return $this->state(function () use ($product) {
            $catalogProduct = $product ?? Product::factory()->create();

            return [
                'product_id' => $catalogProduct->id,
                'name' => $catalogProduct->name,
                'sku' => $catalogProduct->sku,
                'category_id' => $catalogProduct->category_id,
                'unit_id' => $catalogProduct->unit_id,
                'hsn_code' => $catalogProduct->hsn_code,
                'brand' => $catalogProduct->brand,
                'description' => $catalogProduct->description,
            ];
        });
    }

    /**
     * Mark as inactive.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
