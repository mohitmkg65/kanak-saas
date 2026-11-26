<?php

namespace Database\Factories;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'company_name' => $this->faker->optional()->company(),
            'gstin' => $this->faker->optional()->bothify('##?????####?###'),
            'customer_type' => $this->faker->randomElement(CustomerType::cases())->value,
            'billing_address' => $this->faker->streetAddress(),
            'billing_city' => $this->faker->city(),
            'billing_state' => $this->faker->state(),
            'billing_country' => 'India',
            'billing_postal_code' => $this->faker->postcode(),
            'credit_limit' => $this->faker->optional()->randomFloat(2, 10000, 500000),
            'outstanding_balance' => 0.00,
            'status' => CustomerStatus::Active->value,
        ];
    }

    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
        ]);
    }

    public function wholesale(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => CustomerType::Wholesale->value,
        ]);
    }

    public function blacklisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CustomerStatus::Blacklisted->value,
        ]);
    }
}
