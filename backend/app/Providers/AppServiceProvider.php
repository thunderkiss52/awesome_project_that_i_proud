<?php

namespace App\Providers;

use App\Domain\Bookings\Contracts\BookingConflictChecker;
use App\Domain\Bookings\Contracts\BookingRepository;
use App\Domain\Subscriptions\Contracts\SubscriptionQuotaResolver;
use App\Infrastructure\Persistence\EloquentBookingConflictChecker;
use App\Infrastructure\Persistence\EloquentBookingRepository;
use App\Infrastructure\Persistence\EloquentSubscriptionQuotaResolver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookingRepository::class, EloquentBookingRepository::class);
        $this->app->bind(BookingConflictChecker::class, EloquentBookingConflictChecker::class);
        $this->app->bind(SubscriptionQuotaResolver::class, EloquentSubscriptionQuotaResolver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        RateLimiter::for('api', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();

            return Limit::perMinute(60)->by((string) $key);
        });

        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('booking-write', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();

            return Limit::perMinute(20)->by((string) $key);
        });
    }
}
