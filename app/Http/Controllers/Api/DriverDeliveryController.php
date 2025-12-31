<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverTracking;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverDeliveryController extends Controller
{
    /**
     * فتح مسار + تحديث موقع (نفس الدالة)
     */
    public function activeDrivers(): JsonResponse
{
    $drivers = DriverTracking::query()
        ->where('is_tracking', true)
        ->whereNotNull('last_latitude')
        ->whereNotNull('last_longitude')
        ->with(['user:id,name'])
        ->get()
        ->map(function ($track) {
            return [
                'driver_id' => $track->user->id,
                'name'      => $track->user->name,
                'latitude'  => (float) $track->last_latitude,
                'longitude' => (float) $track->last_longitude,
            ];
        });

    return response()->json([
        'status' => true,
        'drivers' => $drivers,
    ]);
}

    public function track(Request $request): JsonResponse
    {
        $request->validate([
            'order_id'  => ['required', 'integer'],
            'latitude'  => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        // السائق الحقيقي (من التوكن)
        $driver = $request->user();

        // فقط تأكد أن الطلب موجود
        $order = Order::findOrFail($request->order_id);

        // فتح المسار أو تحديث الموقع (صف واحد لكل سائق)
        DriverTracking::updateOrCreate(
            ['user_id' => $driver->id],
            [
                'is_tracking'      => true,
                'last_latitude'    => $request->latitude,
                'last_longitude'   => $request->longitude,
                'last_location_at' => now(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'تم فتح المسار / تحديث الموقع',
            'destination' => [
                'latitude'  => $order->latitude,
                'longitude' => $order->longitude,
            ],
        ]);
    }

    /**
     * تم الوصول = إنهاء التتبع وتصفير الموقع
     */
    public function markDelivered(Order $order): JsonResponse
    {
        $driver = request()->user();

        // إيقاف التتبع + تصفير الموقع
        DriverTracking::where('user_id', $driver->id)
            ->update([
                'is_tracking'      => false,
                'last_latitude'    => null,
                'last_longitude'   => null,
                'last_location_at' => null,
            ]);

        // تغيير حالة الطلب
        $order->update([
            'status' => 'delivered',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم الوصول وإنهاء التتبع',
        ]);
    }
}
