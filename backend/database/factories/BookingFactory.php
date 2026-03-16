<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startsAt = now()->addDays(fake()->numberBetween(1, 14))->setTime(fake()->numberBetween(9, 18), 0);

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->addHour(),
            'status' => Booking::STATUS_ACTIVE,
            'cancelled_at' => null,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => Booking::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }
}
