<?php

namespace App\Http\Controllers\Api;

use App\Application\Bookings\ListBookingActivityAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingLogResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityFeedController extends Controller
{
    public function __construct(
        protected ListBookingActivityAction $listBookingActivity,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $limit = min((int) $request->integer('limit', 12), 30);
        $activity = $this->listBookingActivity->handle($user, $limit);

        return response()->json([
            'data' => BookingLogResource::collection($activity),
        ]);
    }
}
