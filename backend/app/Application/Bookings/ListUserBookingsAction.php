<?php

namespace App\Application\Bookings;

use App\Domain\Bookings\Contracts\BookingRepository;
use App\Models\User;
use Illuminate\Support\Collection;

class ListUserBookingsAction
{
    public function __construct(
        protected BookingRepository $bookings,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     * @return Collection<int, \App\Models\Booking>
     */
    public function handle(User $user, array $filters = []): Collection
    {
        return $this->bookings->listForUser($user, $filters);
    }
}
