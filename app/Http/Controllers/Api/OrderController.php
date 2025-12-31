<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\DriverZone;
use App\Helpers\GeoHelper;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * جلب طلبات المستخدم
     */
    public function index()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
        ->latest()
        ->limit(10)->get();

        return response()->json([
            'message' => 'تم جلب الطلبات بنجاح.',
            'data' => OrderResource::collection($orders),
        ]);
    }

    /**
     * إنشاء طلب جديد
     */
    public function store(OrderRequest $request)
    {
        $user = Auth::user();

        // -----------------------------
        // 1) التحقق من الوقت (9 صباحًا - 5 عصرًا)
        // -----------------------------
        $timezone = 'Asia/Baghdad';
        $now = Carbon::now($timezone);
        $start = Carbon::createFromTime(9, 0, 0, $timezone);
        $end   = Carbon::createFromTime(17, 0, 0, $timezone);

        if (! $now->between($start, $end)) {
            return response()->json([
                'success' => false,
                'message' => 'الطلبات متاحة فقط من الساعة 9 صباحًا حتى 5 مساءً (بتوقيت بغداد)',
            ], 422);
        }

        // -----------------------------
       // 2) التحقق من أن نقطة الزبون داخل منطقة التوصيل + معرفة السائق
      // -----------------------------
$point = [
    'lat' => $request->latitude,
    'lng' => $request->longitude,
];

$driverId = null;   // ← يجب تعريف المتغير
$inside   = false;

foreach (DriverZone::all() as $zone) {

    if (GeoHelper::isPointInPolygon($point, $zone->coordinates)) {

        $inside   = true;
        $driverId = $zone->driver_id;   // ← السطر المهم
        break;
    }
}

if (! $inside || ! $driverId) {
    return response()->json([
        'success' => false,
        'message' => 'عذرًا، لا يوجد توصيل في منطقتك حالياً، سنوفرها قريباً.',
    ], 422);
}

        // -----------------------------
// 3) منطق الاشتراك (مفصول تمامًا)
// -----------------------------
$requestedQty = $request->quantity; // الكمية المطلوبة من الزبون

$quantity = 0;   // ❗ لغير المشترك فقط
$subQ     = 0;   // ❗ للمشترك فقط
$price    = 0;

// اشتراك نشط؟
$subscription = Subscription::activeForUser($user->id)->first();

if ($subscription) {

    // ===== مشترك =====
    $remaining = $subscription->total_quantity - $subscription->used_quantity;

    // ❌ ممنوع يطلب فوق اشتراكه
    if ($requestedQty > $remaining) {
        return response()->json([
            'success' => false,
            'message' => 'الكمية المطلوبة أكبر من الرصيد المتبقي في اشتراكك.',
        ], 422);
    }

    // ✔️ الطلب يُحسب فقط على الاشتراك
    $subQ = $requestedQty;   // يزداد عدّاد الاشتراك فقط
    $quantity = 0;           // العادي يبقى صفر
    $price = 0;              // لا سعر للمشترك

    // خصم من الاشتراك
    $subscription->used_quantity += $requestedQty;

    // انتهاء الاشتراك تلقائيًا
    if (
        $subscription->used_quantity >= $subscription->total_quantity ||
        now()->greaterThan($subscription->ends_at)
    ) {
        $subscription->status = 'expired';
    }

    $subscription->save();

} else {

    // ===== غير مشترك =====
    $quantity = $requestedQty;   // يزداد فقط العادي
    $subQ = 0;                   // الاشتراك صفر

    if ($quantity == 1 || $quantity == 2) {
        $price = 1000;
    } else {
        $price = 1000 + (($quantity - 2) * 500);
    }
}

// -----------------------------
// 4) إنشاء الطلب
// -----------------------------
$order = Order::create([
    'user_id'   => $user->id,
    'driver_id' => $driverId,

    // الفصل الواضح
    'quantity'              => $quantity,   // غير مشترك فقط
    'subscription_quantity' => $subQ,        // مشترك فقط

    'price'     => $price,
    'notes'     => $request->notes,
    'latitude'  => $request->latitude,
    'longitude' => $request->longitude,
    'status'    => 'on_delivery',
]);

return response()->json([
    'success' => true,
    'message' => 'تم إرسال الطلب بنجاح.',
    'data'    => new OrderResource($order),
], 201);

    }

    //  تفاصيل الطلب 
    public function show($id)
    {
    $order = Order::with('user')->find($id);

    if (! $order) {
        return response()->json([
            'success' => false,
            'message' => 'الطلب غير موجود.'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => new OrderResource($order)
    ]);
}

    // تم الوصول 
//     public function markDelivered($id)
//     {
//     $order = Order::find($id);

//     if (! $order) {
//         return response()->json([
//             'success' => false,
//             'message' => 'الطلب غير موجود.'
//         ], 404);
//     }

//     $order->status = 'delivered';
//     $order->save();

//     return response()->json([
//         'success' => true,
//         'message' => 'تم تغيير حالة الطلب إلى تم الوصول.'
//     ]); 
// }

    /**
     * حذف الطلب
     */
    public function destroy($id)
    {
        $order = Order::with('user.activeSubscription')->find($id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'الطلب غير موجود.'
            ], 404);
        }

        $subscription = $order->user->activeSubscription;

        // نرجع فقط قناني الاشتراك (subscription_quantity)
        if ($subscription && now()->lessThanOrEqualTo($subscription->ends_at)) {

            $subQ = $order->subscription_quantity;

            $subscription->used_quantity -= $subQ;

            if ($subscription->used_quantity < 0) {
                $subscription->used_quantity = 0;
            }

            $subscription->status = 'active';
            $subscription->save();
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الطلب ومعالجة الاشتراك بنجاح.'
        ]);
    }

    public function driverOrders()
{
    $driver = Auth::user();

    // 1) جلب منطقة السائق
    $zone = DriverZone::where('driver_id', $driver->id)->first();

    // 2) جلب كل الطلبات النشطة
    $orders = Order::where('status', 'on_delivery')->get();

    $final = [];

    foreach ($orders as $order) {

        $point = [
            'lat' => $order->latitude,
            'lng' => $order->longitude,
        ];

        // 3) هل الطلب داخل المنطقة؟
        if (GeoHelper::isPointInPolygon($point, $zone->coordinates)) {

            // ================================
            // 4) تحديد اللون الخاص بكل طلب
            // ================================
            $color = 'black'; // افتراضي: غير مشترك

            if ($order->subscription_quantity > 0 && $order->over_quantity == 0) {
                $color = 'green';   // مشترك ولم يتجاوز
            }

            if ($order->over_quantity > 0) {
                $color = 'red';     // مشترك وتجاوز
            }

            // نضيف الطلب للنتيجة
            $final[] = [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'latitude' => $order->latitude,
                'longitude' => $order->longitude,
                'price' => $order->price,
                'quantity' => $order->quantity,
                'notes' => $order->notes,
                'color' => $color,
            ];
        }
    }

    return response()->json([
        'success' => true,
        'orders' => $final
    ]); }
}
