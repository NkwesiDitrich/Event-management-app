<?php

namespace Database\Factories\Infrastructure\Models;

use App\Infrastructure\Models\RegistrationModel;
use App\Infrastructure\Models\EventModel;
use App\Infrastructure\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationModelFactory extends Factory
{
    protected $model = RegistrationModel::class;

    public function definition(): array
    {
        $registeredAt = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'user_id' => UserModel::factory(),
            'event_id' => EventModel::factory(),
            'registered_at' => $registeredAt,
            'status' => fake()->randomElement(['confirmed', 'cancelled', 'attended', 'no_show']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'refunded', 'failed']),
            'payment_method' => fake()->optional(0.7)->randomElement(['credit_card', 'paypal', 'bank_transfer', 'cash']),
            'payment_reference' => fake()->optional(0.7)->uuid(),
            'amount_paid' => fake()->optional(0.6)->randomFloat(2, 0, 500),
            'notes' => fake()->optional(0.2)->sentence(),
            'checked_in_at' => fake()->optional(0.3)->dateTimeBetween($registeredAt, 'now'),
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn() => [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ]);
    }

    public function cancelled(): static
    {
        $cancelledAt = fake()->dateTimeBetween('-1 month', 'now');
        return $this->state(fn() => [
            'status' => 'cancelled',
            'payment_status' => 'refunded',
            'cancelled_at' => $cancelledAt,
            'cancellation_reason' => fake()->randomElement([
                'Schedule conflict','Personal reasons','Event cancelled',
                'Unable to attend','Changed mind','Emergency'
            ]),
            'checked_in_at' => null,
        ]);
    }

    public function attended(): static
    {
        $registeredAt = fake()->dateTimeBetween('-3 months', '-1 week');
        $checkedInAt = fake()->dateTimeBetween($registeredAt, 'now');
        return $this->state(fn() => [
            'status' => 'attended',
            'payment_status' => 'paid',
            'registered_at' => $registeredAt,
            'checked_in_at' => $checkedInAt,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ]);
    }

    public function noShow(): static
    {
        return $this->state(fn() => [
            'status' => 'no_show',
            'payment_status' => 'paid',
            'checked_in_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn() => [
            'payment_status' => 'paid',
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'payment_reference' => fake()->uuid(),
            'amount_paid' => fake()->randomFloat(2, 10, 500),
        ]);
    }

    public function free(): static
    {
        return $this->state(fn() => [
            'payment_status' => 'paid',
            'payment_method' => null,
            'payment_reference' => null,
            'amount_paid' => 0.00,
        ]);
    }

    public function pendingPayment(): static
    {
        return $this->state(fn() => [
            'payment_status' => 'pending',
            'payment_method' => null,
            'payment_reference' => null,
            'amount_paid' => 0.00,
        ]);
    }

    public function checkedIn(): static
    {
        $registeredAt = fake()->dateTimeBetween('-1 month', '-1 day');
        $checkedInAt = fake()->dateTimeBetween($registeredAt, 'now');
        return $this->state(fn() => [
            'status' => 'attended',
            'registered_at' => $registeredAt,
            'checked_in_at' => $checkedInAt,
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn() => [
            'registered_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'status' => 'confirmed',
        ]);
    }
}
