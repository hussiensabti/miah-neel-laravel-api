<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverDailyStat;
use Illuminate\Http\Request;
use App\Http\Resources\DriverDailyStatResource;

class DriverDailyStatsController extends Controller
{
    // إرجاع قائمة التواريخ المتاحة
    public function dates()
    {
        $dates = DriverDailyStat::select('date')
                 ->distinct()
                 ->orderBy('date', 'desc')
                 ->get()
                 ->pluck('date');

        return response()->json($dates);
    }

    // إحصائيات لكل السائقين ليوم معين
    public function statsByDate($date)
    {
        $stats = DriverDailyStat::where('date', $date)
                 ->get();

        return DriverDailyStatResource::collection($stats);
    }

    // (اختياري) إحصائيات لسائق وحده + يوم معين
    public function statsByDriverAndDate(Request $req, $driverId, $date)
    {
        $stat = DriverDailyStat::where('driver_id', $driverId)
                ->where('date', $date)
                ->firstOrFail();

        return new DriverDailyStatResource($stat);
    }

    public function totalByDate($date)
{
    $stats = DriverDailyStat::where('date', $date)->get();

    return response()->json([
        'active_orders'    => $stats->sum('active_orders'),
        'completed_orders' => $stats->sum('completed_orders'),
        'sub_orders'       => $stats->sum('sub_orders'),
        'net_income'       => $stats->sum('net_income'),
    ]);
}
}
