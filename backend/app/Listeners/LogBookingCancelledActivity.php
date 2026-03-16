<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Models\BookingLog;

class LogBookingCancelledActivity
{
    public function handle(BookingCancelled $event): void
    {
        BookingLog::query()->create([
            'booking_id' => $event->booking->id,
            'user_id' => $event->booking->user_id,
            'event_type' => 'booking.cancelled',
            'old_value' => [
                'status' => 'active',
            ],
            'new_value' => [
                'status' => $event->booking->status,
                'cancelled_at' => $event->booking->cancelled_at?->toIso8601String(),
            ],
            'created_at' => now(),
        ]);
    }
}
