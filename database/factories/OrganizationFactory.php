<?php

namespace Database\Factories;

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationStatus;
use App\Enums\OrganizationSubscriptionStatus;
use App\Enums\RegistrationSource;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->company().' Materials';

        return [
            'name' => $name,
            'legal_name' => $name.' Pvt Ltd',
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'India',
            'postal_code' => fake()->postcode(),
            'status' => OrganizationStatus::ACTIVE->value,
            'approval_status' => OrganizationApprovalStatus::Approved->value,
            'subscription_status' => OrganizationSubscriptionStatus::Trialing->value,
            'registration_source' => RegistrationSource::AdminCreated->value,
            'approved_at' => now(),
            'currency' => 'INR',
            'timezone' => 'Asia/Kolkata',
            'locale' => 'en',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => OrganizationStatus::INACTIVE->value,
            'approval_status' => OrganizationApprovalStatus::Pending->value,
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }
}
