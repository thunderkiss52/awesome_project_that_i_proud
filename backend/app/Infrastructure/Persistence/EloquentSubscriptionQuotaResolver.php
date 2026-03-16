<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Bookings\Contracts\BookingRepository;
use App\Domain\Subscriptions\Contracts\SubscriptionQuotaResolver;
use App\Models\Subscription;
use App\Models\User;

class EloquentSubscriptionQuotaResolver implements SubscriptionQuotaResolver
{
    public function __construct(
        protected BookingRepository $bookings,
    ) {
    }

    public function defaultSubscription(): Subscription
    {
        return Subscription::query()->where('code', 'basic')->firstOrFail();
    }

    public function summaryFor(User $user): array
    {
        $subscription = $user->subscription ?: $this->defaultSubscription();
        $activeBookingsCount = $this->bookings->countFutureActiveForUser($user);

        return [
            'subscription' => $subscription,
            'active_bookings_count' => $activeBookingsCount,
            'remaining_slots' => max($subscription->booking_limit - $activeBookingsCount, 0),
        ];
    }
}
