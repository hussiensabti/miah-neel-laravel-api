<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAccountsController extends Controller
{
    /**
     * 🟢 جلب جميع الأدمنية
     */
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->select('id', 'name', 'phone', 'email', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($admins);
    }

    /**
     * 🟢 إضافة أدمن جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|unique:users,phone|regex:/^07\d{9}$/',
            'email'    => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $admin = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
        ]);

        return response()->json([
            'message' => 'تمت إضافة الأدمن بنجاح',
            'admin'   => $admin,
        ], 201);
    }

    /**
     * 🟡 تعديل كلمة المرور فقط
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $admin = User::where('role', 'admin')->findOrFail($id);

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'تم تحديث كلمة المرور بنجاح',
        ]);
    }

    /**
     * 🔴 حذف أدمن
     */
    public function destroy(string $id)
    {
        // حماية: لا تحذف آخر أدمن
        if (User::where('role', 'admin')->count() <= 1) {
            return response()->json([
                'message' => 'لا يمكن حذف آخر أدمن في النظام',
            ], 422);
        }

        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->delete();

        return response()->json([
            'message' => 'تم حذف الأدمن بنجاح',
        ]);
    }
}
