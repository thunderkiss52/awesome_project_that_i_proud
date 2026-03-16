<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $basic = Subscription::query()->where('code', 'basic')->firstOrFail();
        $premium = Subscription::query()->where('code', 'premium')->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => 'basic@example.com'],
            [
                'name' => 'Basic Demo',
                'password' => Hash::make('password123'),
                'subscription_id' => $basic->id,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'premium@example.com'],
            [
                'name' => 'Premium Demo',
                'password' => Hash::make('password123'),
                'subscription_id' => $premium->id,
            ]
        );
    }
}
