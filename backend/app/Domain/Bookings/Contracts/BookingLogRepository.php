<?php

namespace App\Domain\Bookings\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface BookingLogRepository
{
    /**
     * @return Collection<int, \App\Models\BookingLog>
     */
    public function latestForUser(User $user, int $limit = 12): Collection;
}
