<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // جلب الاشتراك النشط للمستخدم (إن وجد)
        $activeSub = $this->user->activeSubscription;

        return [
            'id'        => $this->id,
            'driver_id' => $this->driver_id,
            'quantity'  => $this->quantity,
            'subscription_quantity' => $this->subscription_quantity,
            'price'     => $this->price,
            'notes'     => $this->notes,
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
            'status'    => $this->status,

            // معلومات الاشتراك الخاصة بالمستخدم
            'subscription' => $activeSub ? [
                'is_subscriber' => true,
                'total'         => $activeSub->total_quantity,
                'used'          => $activeSub->used_quantity,
                'remaining'     => max(0, $activeSub->total_quantity - $activeSub->used_quantity),
                'ends_at'       => $activeSub->ends_at->format('Y-m-d H:i:s'),
            ] : [
                'is_subscriber' => false,
                'remaining'     => 0,
            ],

            // تاريخ الإنشاء (توقيت بغداد)
            'created_at' => $this->created_at
                ? $this->created_at->addHours(3)->format('Y-m-d H:i:s')
                : null,

            // معلومات المستخدم الأساسية
            'user'      => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'phone' => $this->user->phone,
            ],
        ];
    }
}
