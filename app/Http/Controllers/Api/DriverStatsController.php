<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Carbon\Carbon;

class DriverStatsController extends Controller
{
    /**
     * إحصائيات السائق لليوم فقط
     */
    public function today()
    {
        $driver = Auth::user();

        // حدود اليوم الحالي
        $start = Carbon::today();            // 00:00
        $end   = Carbon::today()->endOfDay(); // 23:59:59

        // الطلبات الخاصة بالسائق فقط
        $orders = Order::where('driver_id', $driver->id)
            ->whereBetween('created_at', [$start, $end]);

        // 1) الطلبات الجارية
        $activeCount = (clone $orders)
            ->where('status', 'on_delivery')
            ->count();

        // 2) المكتملة
        $doneCount = (clone $orders)
            ->where('status', 'delivered')
            ->count();

        // 3) صافي الأموال (السعر) — لا علاقة له بالاشتراكات
        $totalMoney = (clone $orders)->sum('price');

        return response()->json([
            'date'            => Carbon::now()->format('Y-m-d'),
            'active_orders'   => $activeCount,
            'completed_orders'=> $doneCount,
            'net_income'      => $totalMoney,
        ]);
    }
}
