<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    public function store(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        // دائما يسجل كـ user
        $role = 'user';

        $user = User::create([
            'name'              => $data['name'],
            'phone'             => $data['phone'],
            'password'          => Hash::make($data['password']),
            'role'              => $role,
        ]);

        // إنشاء توكن Sanctum
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'تم إنشاء الحساب بنجاح.',
            'data'    => [
                'user'  => new UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }
}
