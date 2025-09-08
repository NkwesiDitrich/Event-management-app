<?php

namespace Database\Factories;

use App\Infrastructure\Models\EventModel;
use App\Infrastructure\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Models\EventModel>
 */
class EventFactory extends Factory
{
    protected $model = EventModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventDate = fake()->dateTimeBetween('+1 week', '+6 months');
        $registrationDeadline = fake()->dateTimeBetween('now', $eventDate);
        
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraphs(3, true),
            'event_date' => $eventDate,
            'location' => fake()->address(),
            'capacity' => fake()->optional(0.8)->numberBetween(10, 500), // 80% have capacity limit
            'current_registrations' => 0,
            'status' => fake()->randomElement(['draft', 'pending', 'published']),
            'organizer_id' => UserModel::factory(),
            'image' => fake()->optional(0.6)->imageUrl(800, 400, 'events'),
            'price' => fake()->optional(0.4)->randomFloat(2, 0, 500), // 40% are paid events
            'currency' => 'USD',
            'tags' => fake()->optional(0.7)->randomElements([
                'conference', 'workshop', 'seminar', 'networking', 'training',
                'webinar', 'meetup', 'social', 'business', 'technology',
                'education', 'health', 'sports', 'arts', 'music'
            ], fake()->numberBetween(1, 4)),
            'requirements' => fake()->optional(0.3)->paragraph(),
            'is_featured' => fake()->boolean(10), // 10% chance of being featured
            'registration_deadline' => $registrationDeadline,
            'min_attendees' => fake()->optional(0.3)->numberBetween(5, 20),
        ];
    }

    /**
     * Create a published event.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Create a draft event.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Create a pending event.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Create a cancelled event.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Create a featured event.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'status' => 'published',
        ]);
    }

    /**
     * Create a free event.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0.00,
        ]);
    }

    /**
     * Create a paid event.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 10, 500),
        ]);
    }

    /**
     * Create an event with registrations.
     */
    public function withRegistrations(int $count = null): static
    {
        $registrationCount = $count ?? fake()->numberBetween(1, 50);
        
        return $this->state(fn (array $attributes) => [
            'current_registrations' => $registrationCount,
            'capacity' => $registrationCount + fake()->numberBetween(10, 100),
        ]);
    }

    /**
     * Create an event that's nearly full.
     */
    public function nearlyFull(): static
    {
        $capacity = fake()->numberBetween(20, 100);
        $registrations = $capacity - fake()->numberBetween(1, 5);
        
        return $this->state(fn (array $attributes) => [
            'capacity' => $capacity,
            'current_registrations' => $registrations,
            'status' => 'published',
        ]);
    }

    /**
     * Create an upcoming event.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_date' => fake()->dateTimeBetween('+1 day', '+3 months'),
            'status' => 'published',
        ]);
    }

    /**
     * Create a past event.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_date' => fake()->dateTimeBetween('-6 months', '-1 day'),
            'status' => 'published',
        ]);
    }
}
