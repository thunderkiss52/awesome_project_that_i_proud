<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PrepareBookingCancelledNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public int $tries = 3;

    public function handle(BookingCancelled $event): void
    {
        Log::info('Booking cancelled notification prepared', [
            'booking_id' => $event->booking->id,
            'user_id' => $event->booking->user_id,
        ]);
    }
}
