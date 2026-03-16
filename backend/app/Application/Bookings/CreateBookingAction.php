<?php

namespace App\Application\Bookings;

use App\Domain\Bookings\Contracts\BookingConflictChecker;
use App\Domain\Bookings\Contracts\BookingRepository;
use App\Domain\Subscriptions\Contracts\SubscriptionQuotaResolver;
use App\Events\BookingCreated;
use App\Exceptions\BookingConflictException;
use App\Exceptions\SubscriptionLimitExceededException;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    public function __construct(
        protected BookingRepository $bookings,
        protected BookingConflictChecker $conflicts,
        protected SubscriptionQuotaResolver $quotaResolver,
    ) {
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function handle(User $user, array $attributes): Booking
    {
        $event = null;

        /** @var Booking $booking */
        $booking = DB::transaction(function () use ($user, $attributes, &$event) {
            $owner = User::query()->with('subscription')->lockForUpdate()->findOrFail($user->id);
            $startsAt = Carbon::parse($attributes['starts_at']);
            $endsAt = Carbon::parse($attributes['ends_at']);
            $summary = $this->quotaResolver->summaryFor($owner);

            if ($summary['active_bookings_count'] >= $summary['subscription']->booking_limit) {
                throw new SubscriptionLimitExceededException(
                    sprintf(
                        'Subscription limit exceeded. Your %s plan allows up to %d active bookings.',
                        $summary['subscription']->code,
                        $summary['subscription']->booking_limit,
                    )
                );
            }

            if ($this->conflicts->hasConflict($owner, $startsAt, $endsAt)) {
                throw new BookingConflictException();
            }

            $booking = $this->bookings->create([
                'user_id' => $owner->id,
                'title' => $attributes['title'],
                'description' => $attributes['description'] ?? null,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => Booking::STATUS_ACTIVE,
            ]);

            $event = new BookingCreated($booking);

            return $booking;
        });

        if ($event !== null) {
            event($event);
        }

        return $booking;
    }
}
