<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => Role::where('name', 'MR')->first()?->id ?? 1,
            'designation' => fake()->jobTitle(),
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

    /**
     * Create a demo user with a specific role.
     */
    public function demoUser(string $roleName, string $password = null): static
    {
        $role = Role::where('name', $roleName)->first();
        $strongPassword = $password ?? $this->generateStrongPassword();
        
        return $this->state([
            'name' => fake()->name(),
            'email' => strtolower($roleName) . '.demo@demo.local',
            'password' => Hash::make($strongPassword),
            'role_id' => $role?->id ?? 1,
            'designation' => $role?->description ?? 'Demo User',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create an owner/bootstrap user.
     */
    public function ownerUser(string $email, string $password): static
    {
        $ownerRole = Role::where('name', 'Author')->first();
        
        return $this->state([
            'name' => 'System Owner',
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $ownerRole?->id ?? 1,
            'designation' => 'System Owner',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Generate a strong random password.
     */
    private function generateStrongPassword(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < 16; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        return str_shuffle($password);
    }
}
