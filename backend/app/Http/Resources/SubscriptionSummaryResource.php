<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'subscription' => SubscriptionResource::make($this['subscription']),
            'active_bookings_count' => $this['active_bookings_count'],
            'remaining_slots' => $this['remaining_slots'],
        ];
    }
}
