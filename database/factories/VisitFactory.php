<?php

/**
 * Visit Factory
 *
 * @category Factory
 * @package  Database\Factories
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace Database\Factories;

use App\Models\Visit;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Visit Factory Class
 *
 * @category Factory
 * @package  Database\Factories
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 * @extends  \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-1 month', 'now');
        $endTime = fake()->dateTimeBetween($startTime, $startTime->format('Y-m-d H:i:s') . ' +4 hours');

        return [
            'user_id' => User::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->optional()->phoneNumber(),
            'customer_email' => fake()->optional()->safeEmail(),
            'customer_address' => fake()->address(),
            'visit_type' => fake()->randomElement(['sales', 'service', 'follow_up', 'delivery']),
            'purpose' => fake()->optional()->randomElement(
                [
                    'Client Meeting',
                    'Sales Presentation',
                    'Product Demo',
                    'Follow-up Visit',
                    'Maintenance Check',
                    'Training Session',
                    'Consultation',
                    'Site Survey'
                ]
            ),
            'scheduled_at' => $startTime,
            'actual_start_time' => fake()->optional()->dateTimeBetween($startTime, $endTime),
            'actual_end_time' => fake()->optional()->dateTimeBetween($startTime, $endTime),
            'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled', 'rescheduled']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'notes' => fake()->optional()->paragraph(),
            'outcome' => fake()->optional()->randomElement(
                [
                    'Successful',
                    'Follow-up Required',
                    'Order Placed',
                    'No Interest',
                    'Rescheduled',
                    'Completed'
                ]
            ),
            'latitude' => fake()->optional()->latitude(),
            'longitude' => fake()->optional()->longitude(),
            'location_address' => fake()->optional()->address(),
            'attachments' => fake()->optional()->randomElements(
                [
                    'meeting_notes.pdf',
                    'presentation.pptx',
                    'contract.pdf',
                    'photos.zip'
                ],
                fake()->numberBetween(0, 2)
            ),
            'estimated_duration' => fake()->randomFloat(2, 0.5, 8.0),
            'actual_duration' => fake()->optional()->randomFloat(2, 0.5, 8.0),
            'requires_follow_up' => fake()->boolean(30),
            'follow_up_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'cancellation_reason' => fake()->optional()->sentence(),
            'rescheduled_from' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
        ];
    }

    /**
     * Indicate that the visit is completed.
     *
     * @return static
     */
    public function completed(): static
    {
        return $this->state(
            function (array $attributes) {
                $scheduledAt = $attributes['scheduled_at'];
                
                return [
                    'status' => 'completed',
                    'actual_start_time' => fake()->dateTimeBetween($scheduledAt, $scheduledAt->format('Y-m-d H:i:s') . ' +1 hour'),
                    'actual_end_time' => fake()->dateTimeBetween($scheduledAt, $scheduledAt->format('Y-m-d H:i:s') . ' +4 hours'),
                    'outcome' => fake()->randomElement(['Successful', 'Order Placed', 'Completed']),
                    'notes' => fake()->paragraph(),
                ];
            }
        );
    }

    /**
     * Indicate that the visit is scheduled.
     *
     * @return static
     */
    public function scheduled(): static
    {
        return $this->state(
            fn (array $attributes) => [
                'status' => 'scheduled',
                'actual_start_time' => null,
                'actual_end_time' => null,
                'outcome' => null,
            ]
        );
    }

    /**
     * Indicate that the visit is high priority.
     *
     * @return static
     */
    public function highPriority(): static
    {
        return $this->state(
            fn (array $attributes) => [
                'priority' => 'urgent',
            ]
        );
    }
}