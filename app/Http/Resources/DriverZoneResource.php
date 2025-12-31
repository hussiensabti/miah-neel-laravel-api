<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'driver_id'   => $this->driver_id,
            'driver_name' => $this->driver?->name,
            'driver_phone'=> $this->driver?->phone,
            'coordinates' => $this->coordinates,
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}