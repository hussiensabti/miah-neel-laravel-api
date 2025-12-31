<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    /**
     * إرجاع كل الأيام التي يوجد بها طلبات
     * مثال ناتج:
     * ["2025-01-27", "2025-01-26", "2025-01-25"]
     */
    public function dates()
    {
        $dates = Order::select(DB::raw('DATE(created_at) as day'))
                      ->groupBy('day')
                      ->orderBy('day', 'desc')
                      ->pluck('day');

        return response()->json([
            'success' => true,
            'dates' => $dates,
        ]);
    }

    /**
     * جلب طلبات يوم معين فقط
     * GET /api/admin/orders?date=2025-01-27
     */
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->orderBy('id', 'desc')->get();

        return OrderResource::collection($orders);
    }
}