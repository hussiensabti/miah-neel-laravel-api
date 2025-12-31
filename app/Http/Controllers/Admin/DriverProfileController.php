<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use Illuminate\Http\Request;

class DriverProfileController extends Controller
{
    public function showBasic($driverId)
    {
        $profile = DriverProfile::firstOrCreate(['user_id' => $driverId]);

        return response()->json([
            'car_type' => $profile->car_type,
            'documents' => $profile->documents ?? [],
        ]);
    }

    public function updateBasic(Request $request, $driverId)
    {
        $request->validate([
            'car_type' => 'nullable|string|max:150',
            'documents' => 'nullable|array',
            'documents.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $profile = DriverProfile::firstOrCreate(['user_id' => $driverId]);

        if ($request->filled('car_type')) {
            $profile->car_type = $request->car_type;
        }

        if ($request->hasFile('documents')) {
            $paths = $profile->documents ?? [];

            foreach ($request->file('documents') as $file) {
                $path = $file->store("drivers/docs/{$driverId}", 'public');
                $paths[] = $path;
            }

            $profile->documents = $paths;
        }

        $profile->save();

        return response()->json(['message' => 'تم التحديث بنجاح']);
    }

    public function showLiters($driverId)
    {
        $profile = DriverProfile::firstOrCreate(['user_id' => $driverId]);

        return response()->json([
            'total_liters' => $profile->total_liters,
        ]);
    }

    public function updateLiters(Request $request, $driverId)
    {
        $request->validate([
            'add_liters' => 'required|numeric|min:0.1',
        ]);

        $profile = DriverProfile::firstOrCreate(['user_id' => $driverId]);

        $profile->total_liters += $request->add_liters;
        $profile->save();

        return response()->json([
            'message' => 'تم تحديث اللترات',
            'total_liters' => $profile->total_liters,
        ]);
    }
}
