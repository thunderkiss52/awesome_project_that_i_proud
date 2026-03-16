<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Bookings\Contracts\BookingConflictChecker;
use App\Models\Booking;
use App\Models\User;
use Carbon\CarbonInterface;

class EloquentBookingConflictChecker implements BookingConflictChecker
{
    public function hasConflict(
        User $user,
        CarbonInterface $startsAt,
        CarbonInterface $endsAt,
        ?int $ignoreBookingId = null,
    ): bool {
        return Booking::query()
            ->ownedBy($user)
            ->active()
            ->when($ignoreBookingId !== null, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->overlap($startsAt->toDateTimeString(), $endsAt->toDateTimeString())
            ->exists();
    }
}
