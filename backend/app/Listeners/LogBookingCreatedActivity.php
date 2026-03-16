<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Models\BookingLog;

class LogBookingCreatedActivity
{
    public function handle(BookingCreated $event): void
    {
        BookingLog::query()->create([
            'booking_id' => $event->booking->id,
            'user_id' => $event->booking->user_id,
            'event_type' => 'booking.created',
            'new_value' => [
                'title' => $event->booking->title,
                'starts_at' => $event->booking->starts_at?->toIso8601String(),
                'ends_at' => $event->booking->ends_at?->toIso8601String(),
            ],
            'created_at' => now(),
        ]);
    }
}
