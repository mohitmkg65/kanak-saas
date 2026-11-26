<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        $units = [
            ['name' => 'Kilogram', 'abbreviation' => 'KG'],
            ['name' => 'Litre', 'abbreviation' => 'Ltr'],
            ['name' => 'Piece', 'abbreviation' => 'Pc'],
            ['name' => 'Box', 'abbreviation' => 'Box'],
            ['name' => 'Meter', 'abbreviation' => 'Mtr'],
        ];

        $unit = $this->faker->unique()->randomElement($units);

        return [
            'name' => $unit['name'].' '.$this->faker->unique()->numberBetween(1000, 9999),
            'abbreviation' => $unit['abbreviation'].$this->faker->unique()->numberBetween(10, 99),
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
