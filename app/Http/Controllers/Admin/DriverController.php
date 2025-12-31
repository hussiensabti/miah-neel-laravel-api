<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    // 🟢 عرض جميع السائقين
    public function index()
    {
        $drivers = User::where('role', 'driver')->select('id', 'name', 'phone', 'created_at')->get();
        return response()->json($drivers);
    }

    // 🟢 إضافة سائق جديد
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|unique:users,phone|regex:/^07\d{9}$/',
            'password' => 'required|string|min:6',
        ]);

        $driver = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'driver',
        ]);

        return response()->json(['message' => 'تمت إضافة السائق بنجاح', 'driver' => $driver]);
    }

    // 🟢 تعديل كلمة المرور فقط
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $driver = User::where('role', 'driver')->findOrFail($id);
        $driver->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'تم تعديل كلمة المرور بنجاح']);
    }

    // 🔴 حذف سائق
    public function destroy($id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);
        $driver->delete();

        return response()->json(['message' => 'تم حذف السائق بنجاح']);
    }
}