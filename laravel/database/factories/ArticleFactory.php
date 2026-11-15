<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Article> */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'published_on' => fake()->dateTimeBetween('-1 year')->format('Y-m-d'),
            'body' => fake()->paragraphs(2, true),
            'is_published' => true,
        ];
    }
}
