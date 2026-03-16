<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Bookings\Contracts\BookingLogRepository;
use App\Models\BookingLog;
use App\Models\User;
use Illuminate\Support\Collection;

class EloquentBookingLogRepository implements BookingLogRepository
{
    public function latestForUser(User $user, int $limit = 12): Collection
    {
        return BookingLog::query()
            ->where('user_id', $user->id)
            ->with('booking')
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
