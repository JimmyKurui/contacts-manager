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
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile_phone' => $this->faker->phoneNumber(),
            'company' => $this->faker->optional(0.7)->company(),
            'job_title' => $this->faker->optional(0.6)->jobTitle(),
            'address' => $this->faker->optional(0.5)->streetAddress(),
            'city' => $this->faker->optional(0.6)->city(),
            'state' => $this->faker->optional(0.6)->state(),
            'country' => $this->faker->optional(0.7)->country(),
            'postal_code' => $this->faker->optional(0.5)->postcode(),
            'date_of_birth' => $this->faker->optional(0.4)->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
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
