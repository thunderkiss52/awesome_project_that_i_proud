<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use Illuminate\Support\Facades\Log;

class PrepareBookingCancelledNotification
{
    public function handle(BookingCancelled $event): void
    {
        Log::info('Booking cancelled notification prepared', [
            'booking_id' => $event->booking->id,
            'user_id' => $event->booking->user_id,
        ]);
    }
}
