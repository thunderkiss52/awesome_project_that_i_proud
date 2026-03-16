<?php

namespace App\Listeners;

use App\Events\BookingRescheduled;
use App\Models\BookingLog;

class LogBookingRescheduledActivity
{
    public function handle(BookingRescheduled $event): void
    {
        BookingLog::query()->create([
            'booking_id' => $event->booking->id,
            'user_id' => $event->booking->user_id,
            'event_type' => 'booking.rescheduled',
            'old_value' => [
                'starts_at' => $event->oldStartsAt->toIso8601String(),
                'ends_at' => $event->oldEndsAt->toIso8601String(),
            ],
            'new_value' => [
                'starts_at' => $event->booking->starts_at?->toIso8601String(),
                'ends_at' => $event->booking->ends_at?->toIso8601String(),
            ],
            'created_at' => now(),
        ]);
    }
}
