<?php

namespace Tests\Feature;

use App\Events\BookingCreated;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityFeedApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_personal_activity_feed(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
        ]);

        event(new BookingCreated($booking));

        $this->withHeaders($this->authHeaders($user))
            ->getJson('/api/activity')
            ->assertOk()
            ->assertJsonPath('data.0.event_type', 'booking.created')
            ->assertJsonPath('data.0.booking.id', $booking->id);
    }
}
