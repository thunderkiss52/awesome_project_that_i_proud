<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Bookings\Contracts\BookingRepository;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Collection;

class EloquentBookingRepository implements BookingRepository
{
    public function listForUser(User $user, array $filters = []): Collection
    {
        return Booking::query()
            ->ownedBy($user)
            ->when(
                isset($filters['status']),
                fn ($query) => $query->where('status', $filters['status'])
            )
            ->when(
                isset($filters['date_from']),
                fn ($query) => $query->where('starts_at', '>=', $filters['date_from'])
            )
            ->when(
                isset($filters['date_to']),
                fn ($query) => $query->where('starts_at', '<=', $filters['date_to'])
            )
            ->orderBy('starts_at')
            ->get();
    }

    public function findOwnedById(User $user, int $bookingId): ?Booking
    {
        return Booking::query()
            ->ownedBy($user)
            ->whereKey($bookingId)
            ->first();
    }

    public function create(array $attributes): Booking
    {
        /** @var Booking $booking */
        $booking = Booking::query()->create($attributes);

        return $booking->fresh(['user.subscription']);
    }

    public function update(Booking $booking, array $attributes): Booking
    {
        $booking->fill($attributes);
        $booking->save();

        return $booking->fresh(['user.subscription']);
    }

    public function countFutureActiveForUser(User $user): int
    {
        return Booking::query()
            ->ownedBy($user)
            ->active()
            ->where('ends_at', '>', now())
            ->count();
    }
}
