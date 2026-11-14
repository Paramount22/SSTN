<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Event> */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'type' => fake()->randomElement(array_keys(Event::TYPES)),
            'starts_on' => fake()->dateTimeBetween('-2 years', '+1 year')->format('Y-m-d'),
            'location' => 'Dom ZSVTS, Koceľova 15, Bratislava',
            'organizers' => 'SSTN',
            'intro' => fake()->sentence(12),
            'is_published' => true,
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn () => ['starts_on' => now()->addDays(fake()->numberBetween(1, 120))->format('Y-m-d')]);
    }

    public function past(): static
    {
        return $this->state(fn () => ['starts_on' => now()->subDays(fake()->numberBetween(1, 700))->format('Y-m-d')]);
    }
}
