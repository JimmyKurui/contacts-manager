<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Contact;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dob = $this->faker->optional(0.4)->dateTimeBetween('-50 years', '-18 years');
        return [
            'user_id' => User::factory(),
            'first_name' => $firstName = $this->faker->firstName(),
            'last_name' => $lastName = $this->faker->lastName(),
            'email' => strtolower("{$firstName}.{$lastName}@example.com"),
            'mobile_phone' => $this->faker->phoneNumber(),
            'work_phone' => $this->faker->optional(0.5)->phoneNumber(),
            'company' => $company = $this->faker->optional(0.7)->company(),
            'job_title' => $company ? $this->faker->jobTitle() : null,
            'address' => $this->faker->optional(0.5)->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => $this->faker->country(),
            'postal_code' => $this->faker->optional(0.5)->postcode(),
            'date_of_birth' => $dob ? $dob->format('Y-m-d') : null,
            'notes' => $this->faker->optional(0.3)->paragraph(),
            'is_favorite' => $this->faker->boolean(20),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
