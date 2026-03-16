<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use Illuminate\Support\Facades\Log;

class PrepareBookingCreatedNotification
{
    public function handle(BookingCreated $event): void
    {
        Log::info('Booking created notification prepared', [
            'booking_id' => $event->booking->id,
            'user_id' => $event->booking->user_id,
        ]);
    }
}
