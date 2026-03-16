<?php

namespace Tests;

use Database\Seeders\SubscriptionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SubscriptionSeeder::class);
    }

    /**
     * @return array<string, string>
     */
    protected function authHeaders(\App\Models\User $user): array
    {
        $token = auth('api')->login($user);

        return [
            'Authorization' => 'Bearer '.$token,
        ];
    }

    /**
     * @return array<string, string|null>
     */
    protected function bookingPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Meeting with client',
            'description' => 'Project discussion',
            'starts_at' => now()->addDay()->setTime(14, 0)->toDateTimeString(),
            'ends_at' => now()->addDay()->setTime(15, 0)->toDateTimeString(),
        ], $overrides);
    }
}
