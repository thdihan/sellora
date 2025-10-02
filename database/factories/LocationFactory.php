<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
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
            'name' => fake()->company() . ' ' . fake()->randomElement(['Office', 'Branch', 'Store', 'Warehouse']),
            'description' => fake()->optional()->paragraph(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => fake()->country(),
            'postal_code' => fake()->postcode(),
            'type' => fake()->randomElement(['office', 'branch', 'store', 'warehouse', 'client', 'vendor']),
            'status' => fake()->randomElement(['active', 'inactive']),
            'accuracy' => fake()->optional()->randomFloat(2, 1, 100),
            'altitude' => fake()->optional()->randomFloat(2, -100, 8000),
            'speed' => fake()->optional()->randomFloat(2, 0, 200),
            'heading' => fake()->optional()->randomFloat(2, 0, 360),
            'timestamp' => fake()->optional()->dateTimeThisYear(),
            'ip_address' => fake()->optional()->ipv4(),
            'user_agent' => fake()->optional()->userAgent(),
            'notes' => fake()->optional()->sentence(),
            'is_favorite' => fake()->boolean(20),
            'visit_count' => fake()->numberBetween(0, 100),
            'last_visited_at' => fake()->optional()->dateTimeThisYear(),
        ];
    }

    /**
     * Indicate that the location is an office.
     */
    public function office(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'office',
        ]);
    }

    /**
     * Indicate that the location is a client location.
     */
    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'client',
        ]);
    }

    /**
     * Indicate that the location is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }
}