<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\DriverTracking;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 🔹 المستخدم يملك عدة طلبات
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // 🔹 المستخدم يملك عدة اشتراكات
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // 🔹 الاشتراك الحالي للمستخدم (إن وجد)
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>=', now());
    }

    // 🔹 السائق يملك منطقة واحدة
    public function driverZone()
    {
        return $this->hasOne(DriverZone::class, 'driver_id');
    }

    public function driverProfile()
{
    return $this->hasOne(DriverProfile::class);
}

// اللترات اليومية
    public function driverDailyLiters()
    {
        return $this->hasMany(DriverDailyLiter::class);
    }

public function driverTracking(): HasOne
{
    return $this->hasOne(DriverTracking::class);
}

}