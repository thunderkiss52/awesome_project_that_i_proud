<?php

namespace App\Application\Subscriptions;

use App\Domain\Subscriptions\Contracts\SubscriptionQuotaResolver;
use App\Models\User;

class GetSubscriptionSummaryAction
{
    public function __construct(
        protected SubscriptionQuotaResolver $quotaResolver,
    ) {
    }

    /**
     * @return array{
     *     subscription: \App\Models\Subscription,
     *     active_bookings_count: int,
     *     remaining_slots: int
     * }
     */
    public function handle(User $user): array
    {
        return $this->quotaResolver->summaryFor($user->loadMissing('subscription'));
    }
}
