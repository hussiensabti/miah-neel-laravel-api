<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'phone' => $this->phone,

            // هل لديه اشتراك نشط؟
            'subscribed' => $this->activeSubscription ? true : false,

            'created_at' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
