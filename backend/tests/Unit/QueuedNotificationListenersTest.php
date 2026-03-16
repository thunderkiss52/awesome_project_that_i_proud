<?php

namespace Tests\Unit;

use App\Listeners\PrepareBookingCancelledNotification;
use App\Listeners\PrepareBookingCreatedNotification;
use App\Listeners\PrepareBookingRescheduledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class QueuedNotificationListenersTest extends TestCase
{
    #[Test]
    public function notification_listeners_are_configured_for_the_notifications_queue(): void
    {
        $listeners = [
            new PrepareBookingCreatedNotification(),
            new PrepareBookingCancelledNotification(),
            new PrepareBookingRescheduledNotification(),
        ];

        foreach ($listeners as $listener) {
            $this->assertInstanceOf(ShouldQueue::class, $listener);
            $this->assertSame('notifications', $listener->queue);
            $this->assertSame(3, $listener->tries);
        }
    }
}
