<?php

namespace App\Domain\Bookings\Contracts;

use App\Models\User;
use Carbon\CarbonInterface;

interface BookingConflictChecker
{
    public function hasConflict(
        User $user,
        CarbonInterface $startsAt,
        CarbonInterface $endsAt,
        ?int $ignoreBookingId = null,
    ): bool;
}
