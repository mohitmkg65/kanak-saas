<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        $brands = [
            'UltraTech Cement',
            'Tata Tiscon',
            'Asian Paints',
            'Kajaria Ceramics',
            'Jaquar Bathware',
            'Supreme Pipes',
            'ACC Limited',
            'Ambuja Cement',
            'Jindal Panther',
            'Berger Paints',
            'Somany Ceramics',
            'Cera Sanitaryware',
            'Astral Pipes',
            'Finolex Cables',
        ];

        $name = $this->faker->unique()->randomElement($brands).' '.$this->faker->unique()->numberBetween(100, 9999);
        $code = strtoupper(Str::slug(substr($name, 0, 8), ''));

        return [
            'name' => $name,
            'code' => $code,
            'slug' => Str::slug($name),
            'website' => 'https://www.'.Str::slug($name).'.com',
            'description' => $this->faker->sentence(),
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
