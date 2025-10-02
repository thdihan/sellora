<?php

namespace Database\Factories;

use App\Models\Presentation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Presentation>
 */
class PresentationFactory extends Factory
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
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'file_path' => 'presentations/' . fake()->uuid() . '.pdf',
            'file_name' => fake()->words(3, true) . '.pdf',
            'file_size' => fake()->numberBetween(1024, 10485760),
            'file_type' => fake()->randomElement(['pdf', 'pptx', 'ppt']),
            'category' => fake()->randomElement([
                'Product Training',
                'Sales Presentation',
                'Marketing Material',
                'Company Overview',
                'Technical Documentation',
                'Training Module',
                'Client Presentation'
            ]),
            'tags' => fake()->randomElements([
                'training',
                'sales',
                'marketing',
                'product',
                'technical',
                'client',
                'internal'
            ], fake()->numberBetween(1, 4)),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'privacy_level' => fake()->randomElement(['private', 'public', 'shared']),
            'is_template' => fake()->boolean(20),
            'view_count' => fake()->numberBetween(0, 1000),
            'download_count' => fake()->numberBetween(0, 500),
            'version' => fake()->randomElement(['1.0', '1.1', '2.0']),
        ];
    }

    /**
     * Indicate that the presentation is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'privacy_level' => 'public',
        ]);
    }

    /**
     * Indicate that the presentation is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_template' => true,
            'privacy_level' => 'public',
        ]);
    }

    /**
     * Indicate that the presentation is popular.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'view_count' => fake()->numberBetween(500, 2000),
            'download_count' => fake()->numberBetween(100, 800),
        ]);
    }
}