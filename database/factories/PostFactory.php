<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'user_id' => User::factory(),
        ];
    }

    public function of(User $user): static
    {
        return $this->state(fn (): array => [
            'user_id' => $user->id,
        ]);
    }
}
