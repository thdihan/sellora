<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maxScore = fake()->numberBetween(50, 200);
        $passingScore = $maxScore * fake()->randomFloat(2, 0.6, 0.8);

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement([
                'Product Knowledge',
                'Sales Training',
                'Compliance',
                'Safety',
                'Customer Service',
                'Technical Skills',
                'Leadership',
                'Communication'
            ]),
            'type' => fake()->randomElement(['quiz', 'exam', 'survey']),
            'questions' => $this->generateQuestions(),
            'scoring_method' => fake()->randomElement(['percentage', 'points']),
            'max_score' => $maxScore,
            'passing_score' => $passingScore,
            'time_limit' => fake()->numberBetween(15, 120),
            'attempts_allowed' => fake()->numberBetween(1, 5),
            'is_active' => fake()->boolean(80),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+3 months'),
            'instructions' => fake()->paragraph(),
            'tags' => fake()->randomElements([
                'training',
                'assessment',
                'certification',
                'mandatory',
                'optional',
                'beginner',
                'intermediate',
                'advanced'
            ], fake()->numberBetween(2, 4)),
            'difficulty_level' => fake()->randomElement(['easy', 'medium', 'hard']),
            'estimated_duration' => fake()->numberBetween(10, 90),
            'auto_grade' => fake()->boolean(70),
            'show_results_immediately' => fake()->boolean(60),
            'randomize_questions' => fake()->boolean(40),
            'allow_review' => fake()->boolean(80),
            'certificate_template' => fake()->optional()->word(),
            'completion_message' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Generate sample questions for the assessment.
     *
     * @return array
     */
    private function generateQuestions(): array
    {
        $questions = [];
        $questionCount = fake()->numberBetween(5, 20);

        for ($i = 0; $i < $questionCount; $i++) {
            $questions[] = [
                'id' => $i + 1,
                'type' => fake()->randomElement(['multiple_choice', 'true_false', 'short_answer', 'essay']),
                'question' => fake()->sentence() . '?',
                'options' => fake()->randomElement([
                    ['A) Option 1', 'B) Option 2', 'C) Option 3', 'D) Option 4'],
                    ['True', 'False'],
                    null
                ]),
                'correct_answer' => fake()->randomElement(['A', 'B', 'C', 'D', 'True', 'False']),
                'points' => fake()->numberBetween(1, 10),
                'explanation' => fake()->optional()->sentence(),
            ];
        }

        return $questions;
    }

    /**
     * Indicate that the assessment is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'start_date' => fake()->dateTimeBetween('-1 week', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+2 months'),
        ]);
    }

    /**
     * Indicate that the assessment is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the assessment is certification type.
     */
    public function certification(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'exam',
            'attempts_allowed' => 3,
            'time_limit' => 90,
            'passing_score' => 80,
            'certificate_template' => 'certification_template',
        ]);
    }

    /**
     * Indicate that the assessment is a quick quiz.
     */
    public function quickQuiz(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'quiz',
                'time_limit' => 15,
                'questions' => array_slice($this->generateQuestions(), 0, 5),
                'estimated_duration' => 10,
            ];
        });
    }
}