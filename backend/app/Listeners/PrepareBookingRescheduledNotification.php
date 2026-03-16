<?php

namespace App\Listeners;

use App\Events\BookingRescheduled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PrepareBookingRescheduledNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public int $tries = 3;

    public function handle(BookingRescheduled $event): void
    {
        Log::info('Booking rescheduled notification prepared', [
            'booking_id' => $event->booking->id,
            'user_id' => $event->booking->user_id,
            'old_starts_at' => $event->oldStartsAt->toIso8601String(),
            'new_starts_at' => $event->booking->starts_at?->toIso8601String(),
        ]);
    }
}
