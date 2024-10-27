<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => $this->faker->randomElement(['Tech Trends Blog']),
            'title' => $this->faker->words(3, true),
            'image' => $this->faker->imageUrl(1600, 1084),
            'description' => $this->faker->paragraphs(rand(5, 15), true),
            'author_id' => User::inRandomOrder()->first()->id
        ];
    }
}
