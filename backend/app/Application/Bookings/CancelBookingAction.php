<?php

namespace App\Application\Bookings;

use App\Domain\Bookings\Contracts\BookingRepository;
use App\Events\BookingCancelled;
use App\Exceptions\BookingOperationException;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class CancelBookingAction
{
    public function __construct(
        protected BookingRepository $bookings,
    ) {
    }

    public function handle(Booking $booking): Booking
    {
        if ($booking->status !== Booking::STATUS_ACTIVE) {
            throw new BookingOperationException('Booking is already cancelled.');
        }

        $event = null;

        /** @var Booking $cancelledBooking */
        $cancelledBooking = DB::transaction(function () use ($booking, &$event) {
            $booking = $this->bookings->update($booking, [
                'status' => Booking::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

            $event = new BookingCancelled($booking);

            return $booking;
        });

        if ($event !== null) {
            event($event);
        }

        return $cancelledBooking;
    }
}
