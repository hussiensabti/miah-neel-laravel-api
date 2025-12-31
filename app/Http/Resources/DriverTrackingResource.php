<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverTrackingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'is_tracking'      => (bool) $this->is_tracking,
            'last_latitude'    => $this->last_latitude,
            'last_longitude'   => $this->last_longitude,
            'last_location_at' => optional($this->last_location_at)?->toISOString(),
        ];
    }
}
