<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalAmount = fake()->randomFloat(2, 10000, 500000);
        $allocatedAmount = $totalAmount * fake()->randomFloat(2, 0.7, 1.0);
        $spentAmount = $allocatedAmount * fake()->randomFloat(2, 0, 0.8);
        $remainingAmount = $allocatedAmount - $spentAmount;

        return [
            'name' => fake()->words(3, true) . ' Budget',
            'description' => fake()->paragraph(),
            'period_type' => fake()->randomElement(['monthly', 'quarterly', 'yearly']),
            'start_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+6 months'),
            'total_amount' => $totalAmount,
            'allocated_amount' => $allocatedAmount,
            'spent_amount' => $spentAmount,
            'remaining_amount' => $remainingAmount,
            'status' => fake()->randomElement(['draft', 'active', 'completed', 'cancelled']),
            'created_by' => User::factory(),
            'approved_by' => fake()->optional()->randomElement(User::pluck('id')->toArray()),
            'approved_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'notes' => fake()->optional()->paragraph(),
            'categories' => fake()->randomElements([
                'Marketing',
                'Operations',
                'HR',
                'IT',
                'Sales',
                'Research',
                'Training',
                'Equipment',
                'Travel',
                'Utilities'
            ], fake()->numberBetween(2, 5)),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'BDT']),
            'auto_approve_limit' => fake()->randomFloat(2, 100, 5000),
            'notification_threshold' => fake()->randomFloat(2, 0.7, 0.9),
            'is_recurring' => fake()->boolean(30),
            'recurring_frequency' => fake()->optional()->randomElement(['monthly', 'quarterly', 'yearly']),
        ];
    }

    /**
     * Indicate that the budget is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'approved_by' => User::factory(),
            'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the budget is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Indicate that the budget is recurring.
     */
    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'recurring_frequency' => fake()->randomElement(['monthly', 'quarterly', 'yearly']),
        ]);
    }

    /**
     * Indicate that the budget is high value.
     */
    public function highValue(): static
    {
        return $this->state(function (array $attributes) {
            $totalAmount = fake()->randomFloat(2, 100000, 1000000);
            $allocatedAmount = $totalAmount * fake()->randomFloat(2, 0.8, 1.0);
            $spentAmount = $allocatedAmount * fake()->randomFloat(2, 0, 0.6);
            $remainingAmount = $allocatedAmount - $spentAmount;

            return [
                'total_amount' => $totalAmount,
                'allocated_amount' => $allocatedAmount,
                'spent_amount' => $spentAmount,
                'remaining_amount' => $remainingAmount,
            ];
        });
    }
}