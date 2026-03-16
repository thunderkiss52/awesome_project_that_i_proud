<?php

namespace App\Providers;

use App\Events\BookingCancelled;
use App\Events\BookingCreated;
use App\Events\BookingRescheduled;
use App\Listeners\LogBookingCancelledActivity;
use App\Listeners\LogBookingCreatedActivity;
use App\Listeners\LogBookingRescheduledActivity;
use App\Listeners\PrepareBookingCancelledNotification;
use App\Listeners\PrepareBookingCreatedNotification;
use App\Listeners\PrepareBookingRescheduledNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, list<class-string>>
     */
    protected $listen = [
        BookingCreated::class => [
            LogBookingCreatedActivity::class,
            PrepareBookingCreatedNotification::class,
        ],
        BookingCancelled::class => [
            LogBookingCancelledActivity::class,
            PrepareBookingCancelledNotification::class,
        ],
        BookingRescheduled::class => [
            LogBookingRescheduledActivity::class,
            PrepareBookingRescheduledNotification::class,
        ],
    ];
}
