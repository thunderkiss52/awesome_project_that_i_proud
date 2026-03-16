<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_type' => $this->event_type,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'created_at' => $this->created_at?->toIso8601String(),
            'booking' => $this->whenLoaded('booking', fn () => [
                'id' => $this->booking?->id,
                'title' => $this->booking?->title,
                'status' => $this->booking?->status,
            ]),
        ];
    }
}
