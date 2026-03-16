<?php

namespace App\Application\Bookings;

use App\Domain\Bookings\Contracts\BookingLogRepository;
use App\Models\User;
use Illuminate\Support\Collection;

class ListBookingActivityAction
{
    public function __construct(
        protected BookingLogRepository $bookingLogs,
    ) {
    }

    /**
     * @return Collection<int, \App\Models\BookingLog>
     */
    public function handle(User $user, int $limit = 12): Collection
    {
        return $this->bookingLogs->latestForUser($user, $limit);
    }
}
