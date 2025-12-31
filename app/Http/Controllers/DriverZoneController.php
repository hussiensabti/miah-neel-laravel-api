<?php

namespace App\Http\Controllers;

use App\Models\DriverZone;
use App\Http\Resources\DriverZoneResource;
use Illuminate\Http\Request;

class DriverZoneController extends Controller
{
    // 📋 عرض كل المناطق
    public function index()
    {
        $zones = DriverZone::with('driver:id,name,phone')->get();
        return DriverZoneResource::collection($zones);
    }

    // 📋 عرض منطقة سائق معيّن (منطقة واحدة فقط)
    public function byDriver($driverId)
    {
        $zone = DriverZone::where('driver_id', $driverId)
            ->with('driver:id,name,phone')
            ->first();

        if (!$zone) {
            return response()->json(['message' => 'No zone found'], 404);
        }

        return new DriverZoneResource($zone);
    }

    // 🟢 إنشاء أو تحديث منطقة السائق
    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id',
            'name' => 'nullable|string|max:100',
            'coordinates' => 'required|array|min:3',
            'coordinates.*.lat' => 'required|numeric',
            'coordinates.*.lng' => 'required|numeric',
        ]);

        $zone = DriverZone::updateOrCreate(
            ['driver_id' => $validated['driver_id']],
            [
                'name' => $validated['name'] ?? null,
                'coordinates' => $validated['coordinates'],
            ]
        );

        return new DriverZoneResource($zone->load('driver:id,name,phone'));
    }

    // ❌ حذف منطقة
    public function destroy($id)
    {
        $zone = DriverZone::findOrFail($id);
        $zone->delete();

        return response()->json(['success' => true]);
    }
}
