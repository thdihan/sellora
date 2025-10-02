<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
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
            'amount' => fake()->randomFloat(2, 50, 5000),
            'purpose' => fake()->randomElement([
                'Office Supplies',
                'Travel Expenses',
                'Marketing Materials',
                'Equipment Purchase',
                'Software License',
                'Training & Development',
                'Client Entertainment',
                'Utilities',
                'Maintenance',
                'Consulting Services'
            ]),
            'status' => fake()->randomElement(['Pending', 'Approved', 'Rejected', 'Paid']),
            'description' => fake()->sentence(),
            'vendor' => fake()->company(),
            'receipt_number' => 'RCP-' . fake()->unique()->numberBetween(100000, 999999),
            'expense_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'category' => fake()->randomElement([
                'office_supplies',
                'travel',
                'marketing',
                'equipment',
                'software',
                'training',
                'entertainment',
                'utilities',
                'maintenance',
                'consulting'
            ]),
            'payment_method' => fake()->randomElement(['cash', 'credit_card', 'bank_transfer', 'cheque']),
            'priority' => fake()->randomElement(['Low', 'Medium', 'High', 'Urgent']),
            'notes' => fake()->optional()->paragraph(),
            'approved_by' => fake()->optional()->randomElement(User::pluck('id')->toArray()),
            'approved_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'rejected_reason' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the bill is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejected_reason' => null,
        ]);
    }

    /**
     * Indicate that the bill is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Approved',
            'approved_by' => User::factory(),
            'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'rejected_reason' => null,
        ]);
    }

    /**
     * Indicate that the bill is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Rejected',
            'approved_by' => User::factory(),
            'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'rejected_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the bill is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'Urgent',
            'amount' => fake()->randomFloat(2, 1000, 10000),
        ]);
    }
}