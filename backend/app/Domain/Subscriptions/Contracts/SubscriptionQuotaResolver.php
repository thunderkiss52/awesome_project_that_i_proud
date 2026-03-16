<?php

namespace App\Domain\Subscriptions\Contracts;

use App\Models\Subscription;
use App\Models\User;

interface SubscriptionQuotaResolver
{
    public function defaultSubscription(): Subscription;

    /**
     * @return array{
     *     subscription: Subscription,
     *     active_bookings_count: int,
     *     remaining_slots: int
     * }
     */
    public function summaryFor(User $user): array;
}
