<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->paragraph(),
            'category_id' => Category::factory(),
            'unit_id' => Unit::factory(),
            'hsn_code' => $this->faker->numerify('######'),
            'sku' => strtoupper($this->faker->unique()->bothify('??-####')),
            'brand' => $this->faker->company(),
            'base_price' => $this->faker->randomFloat(2, 10, 10000),
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
