<?php

namespace App\Http\Controllers\Api;

use App\Application\Subscriptions\GetSubscriptionSummaryAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionSummaryResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected GetSubscriptionSummaryAction $getSubscriptionSummary,
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $summary = $this->getSubscriptionSummary->handle($user);

        return response()->json(
            SubscriptionSummaryResource::make($summary)->resolve()
        );
    }
}
