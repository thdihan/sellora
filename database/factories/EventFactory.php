<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+2 months');
        $endDate = $this->faker->dateTimeBetween($startDate, $startDate->format('Y-m-d H:i:s') . ' +4 hours');
        
        return [
            'title' => $this->faker->randomElement([
                'Team Meeting',
                'Product Launch',
                'Training Session',
                'Client Presentation',
                'Strategy Review',
                'Sales Conference',
                'Workshop',
                'Quarterly Review'
            ]),
            'description' => $this->faker->paragraph(2),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'location' => $this->faker->randomElement([
                'Conference Room A',
                'Main Office',
                'Client Site',
                'Online Meeting',
                'Training Center',
                'Auditorium'
            ]),
            'event_type' => $this->faker->randomElement([
                'meeting',
                'appointment',
                'deadline',
                'reminder',
                'personal',
                'holiday',
                'other'
            ]),
            'status' => $this->faker->randomElement([
                'scheduled',
                'in_progress',
                'completed',
                'cancelled'
            ]),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'start_time' => $this->faker->optional()->dateTimeBetween($startDate, $endDate),
            'end_time' => $this->faker->optional()->dateTimeBetween($startDate, $endDate),
            'is_all_day' => $this->faker->boolean(30),
            'color' => $this->faker->optional()->hexColor(),
            'reminder_minutes' => $this->faker->optional()->randomElement([15, 30, 60, 120]),
            'attendees' => $this->faker->optional()->randomElements([
                ['name' => 'John Doe', 'email' => 'john@example.com'],
                ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
                ['name' => 'Bob Johnson', 'email' => 'bob@example.com']
            ], $this->faker->numberBetween(1, 3)),
            'notes' => $this->faker->optional()->paragraph(),
            'recurring_type' => $this->faker->randomElement(['none', 'daily', 'weekly', 'monthly', 'yearly']),
            'recurring_end_date' => $this->faker->optional()->dateTimeBetween('+1 month', '+1 year'),
            'recurring_days' => $this->faker->optional()->randomElements(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], $this->faker->numberBetween(1, 5)),
            'attachments' => $this->faker->optional()->randomElements([
                 ['name' => 'agenda.pdf', 'path' => '/uploads/agenda.pdf'],
                 ['name' => 'presentation.pptx', 'path' => '/uploads/presentation.pptx']
             ], $this->faker->numberBetween(0, 2)),
             'created_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the event is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'status' => 'scheduled',
        ]);
    }

    /**
     * Indicate that the event is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the event is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }
}