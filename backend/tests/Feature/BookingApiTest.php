<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_booking(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/bookings', $this->bookingPayload());

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Booking created successfully')
            ->assertJsonPath('booking.title', 'Meeting with client');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'title' => 'Meeting with client',
            'status' => Booking::STATUS_ACTIVE,
        ]);
    }

    public function test_user_cannot_create_booking_when_subscription_limit_is_exceeded(): void
    {
        $user = User::factory()->create();

        Booking::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/bookings', $this->bookingPayload([
                'starts_at' => now()->addDays(5)->setTime(16, 0)->toDateTimeString(),
                'ends_at' => now()->addDays(5)->setTime(17, 0)->toDateTimeString(),
            ]))
            ->assertStatus(409)
            ->assertJsonPath('message', 'Subscription limit exceeded. Your basic plan allows up to 3 active bookings.');
    }

    public function test_user_cannot_create_conflicting_booking(): void
    {
        $user = User::factory()->create();

        Booking::factory()->create([
            'user_id' => $user->id,
            'starts_at' => now()->addDays(3)->setTime(10, 0),
            'ends_at' => now()->addDays(3)->setTime(11, 0),
        ]);

        $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/bookings', $this->bookingPayload([
                'starts_at' => now()->addDays(3)->setTime(10, 30)->toDateTimeString(),
                'ends_at' => now()->addDays(3)->setTime(11, 30)->toDateTimeString(),
            ]))
            ->assertStatus(409)
            ->assertJsonPath('message', 'Booking time conflicts with an existing meeting.');
    }

    public function test_user_can_reschedule_booking(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'starts_at' => now()->addDays(2)->setTime(10, 0),
            'ends_at' => now()->addDays(2)->setTime(11, 0),
        ]);

        $response = $this->withHeaders($this->authHeaders($user))
            ->putJson("/api/bookings/{$booking->id}", [
                'starts_at' => now()->addDays(2)->setTime(12, 0)->toDateTimeString(),
                'ends_at' => now()->addDays(2)->setTime(13, 0)->toDateTimeString(),
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Booking updated successfully');

        $this->assertDatabaseHas('booking_logs', [
            'booking_id' => $booking->id,
            'event_type' => 'booking.rescheduled',
        ]);
    }

    public function test_user_can_cancel_booking(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->withHeaders($this->authHeaders($user))
            ->deleteJson("/api/bookings/{$booking->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Booking cancelled successfully');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::STATUS_CANCELLED,
        ]);
    }

    public function test_user_cannot_access_another_users_booking(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $owner->id,
        ]);

        $this->withHeaders($this->authHeaders($attacker))
            ->getJson("/api/bookings/{$booking->id}")
            ->assertNotFound();
    }
}
