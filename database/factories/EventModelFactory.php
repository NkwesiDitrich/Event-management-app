<?php

namespace Database\Factories;

use App\Infrastructure\Models\EventModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventModelFactory extends Factory
{
    protected $model = EventModel::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+6 months'),
            'location' => $this->faker->city,
            'capacity' => $this->faker->numberBetween(50, 500),
            'current_registrations' => $this->faker->numberBetween(0, 50),
            'status' => $this->faker->randomElement(['draft', 'published', 'pending']),
            'organizer_id' => null, // will be set in seeder
            'category_id' => null,  // optional if you add relation later
        ];
    }

    /** Published events */
    public function published()
    {
        return $this->state(fn () => ['status' => 'published']);
    }

    /** Draft events */
    public function draft()
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    /** Pending events */
    public function pending()
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    /** Featured events */
    public function featured()
    {
        return $this->state(fn () => [
            'status' => 'published',
            'title' => '⭐ ' . $this->faker->sentence(3),
        ]);
    }

    /** Past events */
    public function past()
    {
        return $this->state(fn () => [
            'event_date' => $this->faker->dateTimeBetween('-6 months', 'now -1 day'),
            'status' => 'published',
        ]);
    }

    /** Upcoming events */
    public function upcoming()
    {
        return $this->state(fn () => [
            'event_date' => $this->faker->dateTimeBetween('now +1 day', '+6 months'),
            'status' => 'published',
        ]);
    }

    /** Nearly full events */
    public function nearlyFull()
    {
        return $this->state(fn () => [
            'capacity' => 100,
            'current_registrations' => 95,
            'status' => 'published',
        ]);
    }

    /** Free events */
    public function free()
    {
        return $this->state(fn () => [
            'status' => 'published',
            'price' => 0,
        ]);
    }

    /** Paid events */
    public function paid()
    {
        return $this->state(fn () => [
            'status' => 'published',
            'price' => $this->faker->randomFloat(2, 5, 100),
        ]);
    }
}
