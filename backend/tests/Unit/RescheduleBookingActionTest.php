<?php

namespace Tests\Unit;

use App\Application\Bookings\RescheduleBookingAction;
use App\Exceptions\BookingOperationException;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RescheduleBookingActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_rescheduling_cancelled_booking(): void
    {
        $this->expectException(BookingOperationException::class);

        $user = User::factory()->create();
        $booking = Booking::factory()->cancelled()->create([
            'user_id' => $user->id,
        ]);

        app(RescheduleBookingAction::class)->handle($user, $booking, [
            'starts_at' => now()->addDays(4)->setTime(12, 0)->toDateTimeString(),
            'ends_at' => now()->addDays(4)->setTime(13, 0)->toDateTimeString(),
        ]);
    }
}
