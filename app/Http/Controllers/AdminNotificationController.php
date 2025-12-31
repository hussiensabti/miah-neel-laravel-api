<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    // عرض جميع الإشعارات (للأدمن)
    public function all()
    {
        // return Notification::orderBy('id', 'desc')->get();
        return Notification::orderBy('id', 'desc')
        ->limit(10)
        ->get();
    }

    // =====================================================
    // 1) إرسال إشعار
    // =====================================================
    public function store(Request $request)
    {
        $request->validate([
            'target'  => 'required|in:drivers,users',
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $target = $request->target;

        // إرسال لجميع السائقين
        if ($target === 'drivers') {

            // الدور الصحيح في جدولك: driver
            $drivers = User::where('role', 'driver')->pluck('id');

            foreach ($drivers as $id) {
                Notification::create([
                    'user_id'     => $id,
                    'target_type' => 'drivers',
                    'title'       => $request->title,
                    'message'     => $request->message,
                ]);
            }

            return response()->json(['success' => true]);
        }

        // إرسال لجميع المستخدمين
        if ($target === 'users') {

            // الدور الصحيح في جدولك: user
            $users = User::where('role', 'user')->pluck('id');

            foreach ($users as $id) {
                Notification::create([
                    'user_id'     => $id,
                    'target_type' => 'users',
                    'title'       => $request->title,
                    'message'     => $request->message,
                ]);
            }

            return response()->json(['success' => true]);
        }
    }

    // =====================================================
    // 2) عرض إشعارات المستخدم
    // =====================================================
    public function index()
    {
        return Notification::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)->get();
    }

    // =====================================================
    // 3) عداد الإشعارات غير المقروءة
    // =====================================================
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread' => $count]);
    }

    // =====================================================
    // 4) تمييز كل الإشعارات كمقروءة
    // =====================================================
    public function markAllRead()
    {
        Notification::where('user_id', Auth::user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    // =====================================================
    // 5) حذف إشعار واحد
    // =====================================================
    public function destroy($id)
    {
        Notification::where('id', $id)->delete();

        return response()->json(['success' => true]);
    }

    // =====================================================
// السائق يرسل إشعار: "وصل السائق" لمستخدم واحد فقط
// =====================================================
public function driverArrived(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    Notification::create([
        'user_id'     => $request->user_id,   // ← المستخدم المُستهدف
        'target_type' => 'single',
        'title'       => 'إشعار',
        'message'     => 'وصل السائق',
    ]);

    return response()->json(['success' => true]);
}
}