<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserFile>
 */
class UserFileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'original_name' => fake()->word() . '.' . fake()->randomElement(['pdf', 'png', 'jpg']),
            'path' => 'files/1/' . fake()->uuid() . '.pdf',
            'disk' => config('files.disk', 'local'),
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1024, 1048576),
        ];
    }
}
