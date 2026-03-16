<?php

namespace App\Http\Controllers\Api;

use App\Application\Bookings\CancelBookingAction;
use App\Application\Bookings\CreateBookingAction;
use App\Application\Bookings\ListUserBookingsAction;
use App\Application\Bookings\RescheduleBookingAction;
use App\Domain\Bookings\Contracts\BookingRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\BookingIndexRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function __construct(
        protected ListUserBookingsAction $listUserBookings,
        protected CreateBookingAction $createBooking,
        protected RescheduleBookingAction $rescheduleBooking,
        protected CancelBookingAction $cancelBooking,
        protected BookingRepository $bookings,
    ) {
    }

    public function index(BookingIndexRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $bookings = $this->listUserBookings->handle($user, $request->validated());

        return response()->json([
            'data' => BookingResource::collection($bookings),
        ]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $booking = $this->createBooking->handle($user, $request->validated());

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => BookingResource::make($booking),
        ], 201);
    }

    public function show(int $id, BookingIndexRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $booking = $this->findOwnedBooking($user, $id);

        return response()->json([
            'booking' => BookingResource::make($booking),
        ]);
    }

    public function update(int $id, UpdateBookingRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $booking = $this->findOwnedBooking($user, $id);
        $booking = $this->rescheduleBooking->handle($user, $booking, $request->validated());

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => BookingResource::make($booking),
        ]);
    }

    public function destroy(int $id, BookingIndexRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $booking = $this->findOwnedBooking($user, $id);
        $booking = $this->cancelBooking->handle($booking);

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => BookingResource::make($booking),
        ]);
    }

    protected function findOwnedBooking(User $user, int $id): Booking
    {
        $booking = $this->bookings->findOwnedById($user, $id);

        if (! $booking) {
            throw (new ModelNotFoundException())->setModel(Booking::class, [$id]);
        }

        return $booking;
    }
}
