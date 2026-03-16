<?php

namespace Tests\Unit;

use App\Domain\Subscriptions\Contracts\SubscriptionQuotaResolver;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionQuotaResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_remaining_slots_from_active_future_bookings(): void
    {
        $user = User::factory()->create();

        Booking::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        Booking::factory()->cancelled()->create([
            'user_id' => $user->id,
        ]);

        Booking::factory()->create([
            'user_id' => $user->id,
            'starts_at' => now()->subDays(2)->setTime(10, 0),
            'ends_at' => now()->subDays(2)->setTime(11, 0),
        ]);

        $summary = app(SubscriptionQuotaResolver::class)->summaryFor($user->load('subscription'));

        $this->assertSame(2, $summary['active_bookings_count']);
        $this->assertSame(1, $summary['remaining_slots']);
        $this->assertSame('basic', $summary['subscription']->code);
    }
}
