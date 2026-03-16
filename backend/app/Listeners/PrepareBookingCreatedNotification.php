<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PrepareBookingCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public int $tries = 3;

    public function handle(BookingCreated $event): void
    {
        Log::info('Booking created notification prepared', [
            'booking_id' => $event->booking->id,
            'user_id' => $event->booking->user_id,
        ]);
    }
}
