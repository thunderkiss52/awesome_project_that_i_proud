<?php

namespace App\Application\Auth;

use App\Domain\Subscriptions\Contracts\SubscriptionQuotaResolver;
use App\Models\User;

class RegisterUserAction
{
    public function __construct(
        protected SubscriptionQuotaResolver $quotaResolver,
    ) {
    }

    /**
     * @param array{name: string, email: string, password: string} $attributes
     */
    public function handle(array $attributes): User
    {
        $subscription = $this->quotaResolver->defaultSubscription();

        return User::query()->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'subscription_id' => $subscription->id,
        ])->load('subscription');
    }
}
