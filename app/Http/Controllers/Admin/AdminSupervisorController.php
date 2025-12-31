<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSupervisorController extends Controller
{
    // 🟢 جلب جميع المشرفين
    public function index()
    {
        $supervisors = User::where('role', 'supervisor')
            ->select('id', 'name', 'phone', 'email', 'created_at')
            ->get();

        return response()->json($supervisors);
    }

    // 🟢 إضافة مشرف جديد
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|unique:users,phone|regex:/^07\d{9}$/',
            'email'    => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $supervisor = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'supervisor',
        ]);

        return response()->json([
            'message' => 'تمت إضافة المشرف بنجاح',
            'supervisor' => $supervisor
        ]);
    }

    // 🟡 تعديل كلمة المرور
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $supervisor = User::where('role', 'supervisor')->findOrFail($id);

        $supervisor->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'تم تعديل كلمة المرور بنجاح']);
    }

    // 🔴 حذف مشرف
    public function destroy($id)
    {
        $supervisor = User::where('role', 'supervisor')->findOrFail($id);
        $supervisor->delete();

        return response()->json(['message' => 'تم حذف المشرف بنجاح']);
    }
}
