<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_type',
        'documents',
        'total_liters',
    ];

    protected $casts = [
        'documents' => 'array',
        'total_liters' => 'double',
    ];

    // علاقة عكسية مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
     // جميع السجلات اليومية
    public function dailyLiters()
    {
        return $this->hasMany(DriverDailyLiter::class, 'user_id', 'user_id');
    }
}
