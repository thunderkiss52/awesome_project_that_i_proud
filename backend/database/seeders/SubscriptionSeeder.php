<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'basic', 'name' => 'Basic', 'booking_limit' => 3],
            ['code' => 'premium', 'name' => 'Premium', 'booking_limit' => 20],
        ] as $plan) {
            Subscription::query()->updateOrCreate(
                ['code' => $plan['code']],
                $plan,
            );
        }
    }
}
