<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $categories = ['Technology', 'Lifestyle', 'Business', 'Education', 'Health'];

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'category' => fake()->randomElement($categories),
            'status' => fake()->randomElement(['draft', 'published']),
            'content' => fake()->paragraphs(3, true),
        ];
    }
}
