<?php

namespace App\Application\Bookings;

use App\Domain\Bookings\Contracts\BookingConflictChecker;
use App\Domain\Bookings\Contracts\BookingRepository;
use App\Events\BookingRescheduled;
use App\Exceptions\BookingConflictException;
use App\Exceptions\BookingOperationException;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RescheduleBookingAction
{
    public function __construct(
        protected BookingRepository $bookings,
        protected BookingConflictChecker $conflicts,
    ) {
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function handle(User $user, Booking $booking, array $attributes): Booking
    {
        if ($booking->status !== Booking::STATUS_ACTIVE) {
            throw new BookingOperationException('Only active bookings can be rescheduled.');
        }

        $event = null;

        /** @var Booking $updatedBooking */
        $updatedBooking = DB::transaction(function () use ($user, $booking, $attributes, &$event) {
            $startsAt = Carbon::parse($attributes['starts_at']);
            $endsAt = Carbon::parse($attributes['ends_at']);

            if ($this->conflicts->hasConflict($user, $startsAt, $endsAt, $booking->id)) {
                throw new BookingConflictException();
            }

            $oldStartsAt = $booking->starts_at->copy();
            $oldEndsAt = $booking->ends_at->copy();

            $booking = $this->bookings->update($booking, [
                'title' => $attributes['title'] ?? $booking->title,
                'description' => array_key_exists('description', $attributes)
                    ? $attributes['description']
                    : $booking->description,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            if (
                ! $oldStartsAt->equalTo($booking->starts_at)
                || ! $oldEndsAt->equalTo($booking->ends_at)
            ) {
                $event = new BookingRescheduled($booking, $oldStartsAt, $oldEndsAt);
            }

            return $booking;
        });

        if ($event !== null) {
            event($event);
        }

        return $updatedBooking;
    }
}
