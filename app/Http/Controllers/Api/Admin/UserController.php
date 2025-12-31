<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminUserResource;
use App\Models\User;
use App\Models\Subscription;

// ⭐ إضافة الموديل
use App\Models\SubscriptionNotification;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // جلب كل المستخدمين (role=user فقط)
    public function index()
    {
        $users = User::where('role', 'user')->with('activeSubscription')->get();
        return AdminUserResource::collection($users);
    }

    // تبديل "مشترك / غير مشترك"
    public function toggleSubscription($userId)
    {
        $user = User::findOrFail($userId);

        // هل لديه اشتراك نشط؟
        $active = $user->activeSubscription;

        if ($active) {
            // إلغاء الاشتراك
            $active->status = 'expired';
            $active->save();

            // ⭐ إشعار الإلغاء مع تفاصيل الزبون
            SubscriptionNotification::create([
                'user_id' => $user->id,
                'message' => "تم إلغاء الاشتراك\nالاسم: {$user->name}\nالرقم: {$user->phone}\nID: {$user->id}",
            ]);

            return response()->json([
                'success' => true,
                'subscribed' => false,
                'message' => 'تم إلغاء الاشتراك.'
            ]);
        }

        // إنشاء اشتراك جديد
        Subscription::create([
            'user_id' => $user->id,
            'total_quantity' => 14,
            'used_quantity' => 0,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        // ⭐ إشعار التفعيل مع تفاصيل الزبون
        SubscriptionNotification::create([
            'user_id' => $user->id,
            'message' => "تم تفعيل اشتراك جديد\nالاسم: {$user->name}\nالرقم: {$user->phone}\nID: {$user->id}",
        ]);

        return response()->json([
            'success' => true,
            'subscribed' => true,
            'message' => 'تم تفعيل الاشتراك لمدة 30 يوم.'
        ]);
    }

    // بيان تفاصيل الاشتراك
    public function subscriptionDetails($userId)
    {
        $user = User::with('activeSubscription')->findOrFail($userId);

        $sub = $user->activeSubscription;

        if (!$sub) {
            return response()->json([
                'subscribed' => false,
                'message' => 'المستخدم غير مشترك.'
            ]);
        }

        return response()->json([
            'subscribed' => true,
            'remaining_quantity' => $sub->total_quantity - $sub->used_quantity,
            'days_left' => now()->diffInDays($sub->ends_at, false),
            'ends_at' => $sub->ends_at,
        ]);
    }

    // ⭐ دالة عرض السجلات
    public function logs()
    {
        $logs = SubscriptionNotification::with('user')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
