<?php

namespace App\Domain\Bookings\Contracts;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Collection;

interface BookingRepository
{
    /**
     * @param array<string, mixed> $filters
     * @return Collection<int, Booking>
     */
    public function listForUser(User $user, array $filters = []): Collection;

    public function findOwnedById(User $user, int $bookingId): ?Booking;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Booking;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Booking $booking, array $attributes): Booking;

    public function countFutureActiveForUser(User $user): int;
}
