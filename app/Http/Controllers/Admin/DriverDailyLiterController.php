<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverDailyLiter;
use App\Http\Resources\DriverDailyLiterResource;
use Illuminate\Http\Request;

class DriverDailyLiterController extends Controller
{
    /**
     * عرض اللترات اليومية لسائق
     */
    public function index(Request $request, $driverId)
    {
        $query = DriverDailyLiter::where('user_id', $driverId);

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        return DriverDailyLiterResource::collection(
            $query->orderByDesc('date')->get()
        );
    }

    /**
     * عرض يوم محدد
     */
    public function show($driverId, $date)
    {
        $record = DriverDailyLiter::where('user_id', $driverId)
            ->whereDate('date', $date)
            ->firstOrFail();

        return new DriverDailyLiterResource($record);
    }
}