<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverDailyStatResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'driver_id'       => $this->driver_id,
            'date'            => $this->date,
            'active_orders'   => $this->active_orders,
            'completed_orders'=> $this->completed_orders,
            'sub_orders'      => $this->sub_orders,
            'net_income'      => $this->net_income,
        ];
    }
}
