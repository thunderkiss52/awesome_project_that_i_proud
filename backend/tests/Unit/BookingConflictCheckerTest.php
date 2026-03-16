<?php

namespace Tests\Unit;

use App\Domain\Bookings\Contracts\BookingConflictChecker;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingConflictCheckerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_detects_time_overlap_for_active_bookings(): void
    {
        $user = User::factory()->create();

        $existing = Booking::factory()->create([
            'user_id' => $user->id,
            'starts_at' => now()->addDays(2)->setTime(10, 0),
            'ends_at' => now()->addDays(2)->setTime(11, 0),
        ]);

        $checker = app(BookingConflictChecker::class);

        $this->assertTrue(
            $checker->hasConflict(
                $user,
                now()->addDays(2)->setTime(10, 30),
                now()->addDays(2)->setTime(11, 30),
            )
        );

        $this->assertFalse(
            $checker->hasConflict(
                $user,
                now()->addDays(2)->setTime(12, 0),
                now()->addDays(2)->setTime(13, 0),
                $existing->id,
            )
        );
    }
}
